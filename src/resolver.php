<?php

use Eno\Errors\Resolution;

function consolidate(stdClass $context, stdClass $instruction, stdClass $template) : void {
  if($instruction->type == 'SECTION') {
    if($template->type == 'SECTION') {
      mergeSections($instruction, $template, $instruction->deep_copy);
    }

    if($template->type == 'BLOCK') {
      throw Resolution::copyingBlockIntoSection($context, $instruction);
    }

    if($template->type == 'FIELDSET') {
      throw Resolution::copyingFieldsetIntoSection($context, $instruction);
    }

    if($template->type == 'FIELD') {
      throw Resolution::copyingFieldIntoSection($context, $instruction);
    }

    if($template->type == 'LIST') {
      throw Resolution::copyingListIntoSection($context, $instruction);
    }

  } else if($instruction->type == 'NAME') {

    if($template->type == 'BLOCK') {
      $instruction->type = 'FIELD';
      copyBlock($instruction, $template);
    }

    if($template->type == 'FIELD') {
      $instruction->type = 'FIELD';
      copyField($instruction, $template);
    }

    if($template->type == 'FIELDSET') {
      $instruction->type = 'FIELDSET';
      copyGeneric($instruction, $template);
    }

    if($template->type == 'LIST') {
      $instruction->type = 'LIST';
      copyGeneric($instruction, $template);
    }

    if($template->type == 'SECTION') {
      throw Resolution::copyingSectionIntoEmpty($context, $instruction);
    }

  } else if($instruction->type == 'FIELDSET') {

    if($template->type == 'FIELDSET') {
      mergeFieldsets($instruction, $template);
    }

    if($template->type == 'BLOCK') {
      throw Resolution::copyingBlockIntoFieldset($context, $instruction);
    }

    if($template->type == 'FIELD') {
      throw Resolution::copyingFieldIntoFieldset($context, $instruction);
    }

    if($template->type == 'LIST') {
      throw Resolution::copyingListIntoFieldset($context, $instruction);
    }

    if($template->type == 'SECTION') {
      throw Resolution::copyingSectionIntoFieldset($context, $instruction);
    }

  } else if($instruction->type == 'LIST') {
    if($template->type == 'LIST') {
      copyGeneric($instruction, $template);
    }

    if($template->type == 'BLOCK') {
      throw Resolution::copyingBlockIntoList($context, $instruction);
    }

    if($template->type == 'FIELD') {
      throw Resolution::copyingFieldIntoList($context, $instruction);
    }

    if($template->type == 'FIELDSET') {
      throw Resolution::copyingFieldsetIntoList($context, $instruction);
    }

    if($template->type == 'SECTION') {
      throw Resolution::copyingSectionIntoList($context, $instruction);
    }

  } else if($instruction->type == 'FIELD') {

    if($template->type == 'FIELD') {
      copyField($instruction, $template);
    }

    if($template->type == 'BLOCK') {
      copyBlock($instruction, $template);
    }

    if($template->type == 'FIELDSET') {
      throw Resolution::copyingFieldsetIntoField($context, $instruction);
    }

    if($template->type == 'LIST') {
      throw Resolution::copyingListIntoField($context, $instruction);
    }

    if($template->type == 'SECTION') {
      throw Resolution::copyingSectionIntoField($context, $instruction);
    }
  }
}

function copyBlock(stdClass $instruction, stdClass $template) : void {
  $cloned = clone $template;
  array_unshift($instruction->subinstructions, $cloned);
}

function copyField(stdClass $instruction, stdClass $template) : void {
  if($template->value) {
    array_unshift($instruction->subinstructions, (object) [
      'index' => $template->index,
      'length' => $template->length,
      'line' => $template->line,
      'ranges' => $template->ranges,
      'separator' => "\n",
      'type' => 'CONTINUATION',
      'value' => $template->value
    ]);
  }

  copyGeneric($instruction, $template);
}

function copyGeneric(stdClass $instruction, stdClass $template) : void {
  for(end($template->subinstructions); key($template->subinstructions) !== null; prev($template->subinstructions)) {
    $template_subinstruction = current($template->subinstructions);

    if($template_subinstruction->type == 'NOOP')
      continue;

    $cloned = clone $template_subinstruction;
    array_unshift($instruction->subinstructions, $cloned);
  }
}

function mergeFieldsets(stdClass $instruction, stdClass $template) : void {
  $existing_entry_names = [];
  foreach($instruction->subinstructions as $subinstruction) {
    if($subinstruction->type == 'FIELDSET_ENTRY') {
      $existing_entry_names[] = $subinstruction->name;
    }
  }

  for(end($template->subinstructions); key($template->subinstructions) !== null; prev($template->subinstructions)) {
    $template_subinstruction = current($template->subinstructions);

    if($template_subinstruction->type !== 'FIELDSET_ENTRY')
      continue;

    if(!in_array($template_subinstruction->name, $existing_entry_names)) {
      $cloned = clone $template_subinstruction;
      array_unshift($instruction->subinstructions, $cloned);
    }
  }
}

function mergeSections(stdClass $instruction, stdClass $template, bool $deep_merge) : void {
  $existing_subinstructions_name_index = [];

  for(end($template->subinstructions); key($template->subinstructions) !== null; prev($template->subinstructions)) {
    $template_subinstruction = current($template->subinstructions);

    if($template_subinstruction->type == 'NOOP') continue;

    if(!array_key_exists($template_subinstruction->name, $existing_subinstructions_name_index)) {
      $existing_subinstructions_name_index[$template_subinstruction->name] =
        array_filter($instruction->subinstructions, function($instruction) use ($template_subinstruction) {
          return $instruction->name == $template_subinstruction->name;
        });
    }

    $existing_subinstructions = $existing_subinstructions_name_index[$template_subinstruction->name];

    if(count($existing_subinstructions) == 0) {
      $cloned = clone $template_subinstruction;
      array_unshift($instruction->subinstructions, $cloned);
      continue;
    }

    if(!$deep_merge || count($existing_subinstructions) > 1) {
      continue;
    }

    if($template_subinstruction->type == 'FIELDSET' &&
       $existing_subinstructions[0]->type == 'FIELDSET') {

      $template_subinstructions_with_same_name =
        array_filter($template->subinstructions, function($instruction) use($template_subinstruction) {
          return $instruction->name == $template_subinstruction->name;
        });

      if(count($template_subinstructions_with_same_name) == 1) {
        mergeFieldsets($existing_subinstructions[0], $template_subinstruction);
      }
    }

    if($template_subinstruction->type == 'SECTION' &&
       $existing_subinstructions[0]->type == 'SECTION') {

      $template_subinstructions_with_same_name =
        array_filter($template->subinstructions, function($instruction) use($template_subinstruction) {
          return $instruction->name == $template_subinstruction->name;
        });

      if(count($template_subinstructions_with_same_name) == 1) {
        mergeSections($existing_subinstructions[0], $template_subinstruction, true);
      }
    }
  }
}

function recursiveResolve(stdClass $context, stdClass $instruction, array $previous_instructions = []) : void {
  if($instruction->type == 'NOOP') return;

  if(in_array($instruction, $previous_instructions)) {
    throw Resolution::cyclicDependency($context, $instruction, $previous_instructions);
  }

  if($instruction->type == 'SECTION') {
    foreach($instruction->subinstructions as $subinstruction) {
      recursiveResolve($context, $subinstruction, array_merge($previous_instructions, [$instruction]));
    }
  }

  if(isset($instruction->template) && !isset($instruction->consolidated)) {
    if(!array_key_exists($instruction->template, $context->template_index)) {
      throw Resolution::templateNotFound($context, $instruction);
    }

    $templates = $context->template_index[$instruction->template];

    if(count($templates) > 1) {
      throw Resolution::multipleTemplatesFound($context, $instruction, $templates);
    }

    $template = $templates[0];

    recursiveResolve($context, $template, array_merge($previous_instructions, [$instruction]));
    consolidate($context, $instruction, $template);

    $instruction->consolidated = true;

    $index = array_search($instruction, $context->unresolved_instructions);
    unset($context->unresolved_instructions[$index]);
  }
}

function resolve(stdClass $context) : void {
  while(count($context->unresolved_instructions) > 0) {
    recursiveResolve($context, $context->unresolved_instructions[0]);
  }
}
