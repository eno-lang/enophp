<?php

namespace Eno\Errors;
use Eno\ParseError;

class Analysis {

  public static function fieldsetEntryInField($context, $entry_instruction, $field_instruction) {
    $message = $context->messages['analysis']['fieldset_entry_in_field'](
      $entry_instruction->line + $context->indexing
    );

    $marked = array_merge([$field_instruction], $field_instruction->subinstructions);
    $snippet = $context->reporter::report($context, $entry_instruction, $marked);

    $selection = [
      [$entry_instruction->line, 0],
      [$entry_instruction->line, $entry_instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function fieldsetEntryInList($context, $entry_instruction, $list_instruction) {
    $message = $context->messages['analysis']['fieldset_entry_in_list'](
      $entry_instruction->line + $context->indexing
    );

    $marked = array_merge([$list_instruction], $list_instruction->subinstructions);
    $snippet = $context->reporter::report($context, $entry_instruction, $marked);

    $selection = [
      [$entry_instruction->line, 0],
      [$entry_instruction->line, $entry_instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function duplicateFieldsetEntryName($context, $fieldset_instruction, $entry_instruction) {
    $previous_entry_instruction = null;
    foreach($fieldset_instruction->subinstructions as $instruction) {
      if($instruction->name === $entry_instruction->name) {
        $previous_entry_instruction = $instruction;
        break;
      }
    }

    $message = $context->messages['analysis']['duplicate_fieldset_entry_name'](
      $fieldset_instruction->name,
      $entry_instruction->name
    );

    $emphasized = [$entry_instruction, $previous_entry_instruction];
    $marked = array_merge([$fieldset_instruction], $fieldset_instruction->subinstructions);
    $snippet = $context->reporter::report($context, $emphasized, $marked);

    $selection = [
      [$entry_instruction->line, 0],
      [$entry_instruction->line, $entry_instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function listItemInField($context, $item_instruction, $field_instruction) {
    $message = $context->messages['analysis']['list_item_in_field'](
      $item_instruction->line + $context->indexing
    );

    $marked = array_merge([$field_instruction], $field_instruction->subinstructions);
    $snippet = $context->reporter::report($context, $item_instruction, $marked);

    $selection = [
      [$item_instruction->line, 0],
      [$item_instruction->line, $item_instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function listItemInFieldset($context, $item_instruction, $fieldset_instruction) {
    $message = $context->messages['analysis']['list_item_in_fieldset'](
      $item_instruction->line + $context->indexing
    );

    $marked = array_merge([$fieldset_instruction], $fieldset_instruction->subinstructions);
    $snippet = $context->reporter::report($context, $item_instruction, $marked);

    $selection = [
      [$item_instruction->line, 0],
      [$item_instruction->line, $item_instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function missingElementForContinuation($context, $instruction) {
    $message = $context->messages['analysis']['missing_element_for_continuation'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function missingNameForFieldsetEntry($context, $instruction) {
    $message = $context->messages['analysis']['missing_name_for_fieldset_entry'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function missingNameForListItem($context, $instruction) {
    $message = $context->messages['analysis']['missing_name_for_list_item'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function sectionHierarchyLayerSkip($context, $section_instruction, $super_section_instruction) {
    $message = $context->messages['analysis']['section_hierarchy_layer_skip'](
      $section_instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $section_instruction, $super_section_instruction);

    $selection = [
      [$section_instruction->line, 0],
      [$section_instruction->line, $section_instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }
}
