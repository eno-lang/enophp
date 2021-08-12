<?php declare(strict_types=1);

use Eno\Errors\Analysis;

function analyze(stdClass $context) : void {
  $context->document_instruction = (object) [
    'depth' => 0,
    'index' => 0,
    'length' => 0,
    'line' => 1,
    'name' => '<>#:=|\\_ENO_DOCUMENT',
    'ranges' => [
      'section_operator' => [0, 0],
      'name' => [0, 0]
    ],
    'subinstructions' => []
  ];

  $context->template_index = [];
  $context->unresolved_instructions = [];

  $last_continuable_instruction = null;
  $last_fieldset_entry_names = null;
  $last_name_instruction = null;
  $last_section_instruction = $context->document_instruction;
  $active_section_instructions = [$context->document_instruction];
  $unresolved_idle_instructions = [];

  foreach($context->instructions as $instruction) {

    if($instruction->type === 'NOOP') {
      if($last_name_instruction) {
        $unresolved_idle_instructions[] = $instruction;
      } else {
        $last_section_instruction->subinstructions[] = $instruction;
      }

      continue;
    }

    // TODO: Appending block content instructions to the block as subinstructions
    //       is probably not necessary anymore in the new architecture, this could
    //       save performance if we can omit it, investigate and follow up.
    if($instruction->type === 'BLOCK_CONTENT') {
      $last_name_instruction->subinstructions[] = $instruction;
      continue;
    }

    if($instruction->type === 'FIELD') {
      $last_section_instruction->subinstructions = array_merge($last_section_instruction->subinstructions, $unresolved_idle_instructions);
      $unresolved_idle_instructions = [];

      $instruction->subinstructions = [];

      $last_continuable_instruction = $instruction;
      $last_name_instruction = $instruction;

      if(array_key_exists($instruction->name, $context->template_index)) {
        $context->template_index[$instruction->name][] = $instruction;
      } else {
        $context->template_index[$instruction->name] = [$instruction];
      }

      $last_section_instruction->subinstructions[] = $instruction;

      continue;
    }

    if($instruction->type === 'NAME') {
      $last_section_instruction->subinstructions = array_merge($last_section_instruction->subinstructions, $unresolved_idle_instructions);
      $unresolved_idle_instructions = [];

      $instruction->subinstructions = [];

      $last_continuable_instruction = $instruction;
      $last_name_instruction = $instruction;

      if(property_exists($instruction, 'template')) {
        $context->unresolved_instructions[] = $instruction;
      }

      if(!array_key_exists('template', $instruction) || $instruction->name !== $instruction->template) {
        if(array_key_exists($instruction->name, $context->template_index)) {
          $context->template_index[$instruction->name][] = $instruction;
        } else {
          $context->template_index[$instruction->name] = [$instruction];
        }
      }

      $last_section_instruction->subinstructions[] = $instruction;

      continue;
    }

    if($instruction->type === 'LIST_ITEM') {
      if($last_name_instruction === null) {
        throw Analysis::missingNameForListItem($context, $instruction);
      }

      $last_name_instruction->subinstructions = array_merge($last_name_instruction->subinstructions, $unresolved_idle_instructions);
      $unresolved_idle_instructions = [];

      $instruction->name = $last_name_instruction->name;
      $instruction->subinstructions = [];

      if($last_name_instruction->type === 'LIST') {
        $last_name_instruction->subinstructions[] = $instruction;
        $last_continuable_instruction = $instruction;
        continue;
      }

      if($last_name_instruction->type === 'NAME') {
        $last_name_instruction->type = 'LIST';
        $last_name_instruction->subinstructions[] = $instruction;
        $last_continuable_instruction = $instruction;
        continue;
      }

      if($last_name_instruction->type === 'FIELDSET') {
        throw Analysis::listItemInFieldset($context, $instruction, $last_name_instruction);
      }

      if($last_name_instruction->type === 'FIELD') {
        throw Analysis::listItemInField($context, $instruction, $last_name_instruction);
      }
    }

    if($instruction->type === 'FIELDSET_ENTRY') {
      if($last_name_instruction === null) {
        throw Analysis::missingNameForFieldsetEntry($context, $instruction);
      }

      $last_name_instruction->subinstructions = array_merge($last_name_instruction->subinstructions, $unresolved_idle_instructions);
      $unresolved_idle_instructions = [];

      $instruction->subinstructions = [];

      if($last_name_instruction->type === 'FIELDSET') {
        if(in_array($instruction->name, $last_fieldset_entry_names)) {
          throw Analysis::duplicateFieldsetEntryName($context, $last_name_instruction, $instruction);
        } else {
          $last_fieldset_entry_names[] = $instruction->name;
        }

        $last_name_instruction->subinstructions[] = $instruction;
        $last_continuable_instruction = $instruction;
        continue;
      }

      if($last_name_instruction->type === 'NAME') {
        $last_name_instruction->type = 'FIELDSET';
        $last_name_instruction->subinstructions[] = $instruction;
        $last_fieldset_entry_names = [$instruction->name];
        $last_continuable_instruction = $instruction;
        continue;
      }

      if($last_name_instruction->type === 'LIST') {
        throw Analysis::fieldsetEntryInList($context, $instruction, $last_name_instruction);
      }

      if($last_name_instruction->type === 'FIELD') {
        throw Analysis::fieldsetEntryInField($context, $instruction, $last_name_instruction);
      }
    }

    if($instruction->type ===  'BLOCK') {
      $last_section_instruction->subinstructions = array_merge($last_section_instruction->subinstructions, $unresolved_idle_instructions);
      $unresolved_idle_instructions = [];

      $instruction->subinstructions = [];

      $last_continuable_instruction = null;
      $last_name_instruction = $instruction;

      if(array_key_exists($instruction->name, $context->template_index)) {
        $context->template_index[$instruction->name][] = $instruction;
      } else {
        $context->template_index[$instruction->name] = [$instruction];
      }

      $last_section_instruction->subinstructions[] = $instruction;

      continue;
    }

    if($instruction->type === 'BLOCK_TERMINATOR') {
      $last_name_instruction->subinstructions[] = $instruction;
      $last_name_instruction = null;
      continue;
    }

    if($instruction->type === 'CONTINUATION') {
      if($last_continuable_instruction === null) {
        throw Analysis::missingElementForContinuation($context, $instruction);
      }

      $last_continuable_instruction->subinstructions = array_merge($last_continuable_instruction->subinstructions, $unresolved_idle_instructions);
      $unresolved_idle_instructions = [];

      if($last_name_instruction->type === 'NAME') {
        $last_name_instruction->type = 'FIELD';
        $last_name_instruction->value = null;
      }

      $last_continuable_instruction->subinstructions[] = $instruction;
      continue;
    }

    if($instruction->type === 'SECTION') {
      $last_section_instruction->subinstructions = array_merge($last_section_instruction->subinstructions, $unresolved_idle_instructions);
      $unresolved_idle_instructions = [];

      if($instruction->depth - $last_section_instruction->depth > 1) {
        throw Analysis::sectionHierarchyLayerSkip($context, $instruction, $last_section_instruction);
      }

      if($instruction->depth > $last_section_instruction->depth) {
        $last_section_instruction->subinstructions[] = $instruction;
      } else {
        while($active_section_instructions[count($active_section_instructions) - 1]->depth >= $instruction->depth) {
          array_pop($active_section_instructions);
        }

        $active_section_instructions[count($active_section_instructions) - 1]->subinstructions[] = $instruction;
      }

      $last_continuable_instruction = null;
      $last_name_instruction = null;
      $last_section_instruction = $instruction;
      $active_section_instructions[] = $instruction;

      if(property_exists($instruction, 'template')) {
        $context->unresolved_instructions[] = $instruction;
      }

      if(!property_exists($instruction, 'template') || $instruction->name !== $instruction->template) {
        if(array_key_exists($instruction->name, $context->template_index)) {
          $context->template_index[$instruction->name][] = $instruction;
        } else {
          $context->template_index[$instruction->name] = [$instruction];
        }
      }

      $instruction->subinstructions = [];

      continue;
    }

  } // ends foreach($context->instructions as $instruction)

  if(count($unresolved_idle_instructions) > 0) {
    $last_section_instruction->subinstructions = array_merge($last_section_instruction->subinstructions, $unresolved_idle_instructions);
    $unresolved_idle_instructions = [];
  }
}
