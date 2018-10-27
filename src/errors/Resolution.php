<?php

namespace Eno\Errors;
use Eno\ParseError;

class Resolution {
  public static function copyingBlockIntoFieldset($context, $instruction) {
    $message = $context->messages['resolution']['copying_block_into_fieldset'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingBlockIntoList($context, $instruction) {
    $message = $context->messages['resolution']['copying_block_into_list'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingBlockIntoSection($context, $instruction) {
    $message = $context->messages['resolution']['copying_block_into_section'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingFieldsetIntoField($context, $instruction) {
    $message = $context->messages['resolution']['copying_fieldset_into_field'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingFieldsetIntoList($context, $instruction) {
    $message = $context->messages['resolution']['copying_fieldset_into_list'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingFieldsetIntoSection($context, $instruction) {
    $message = $context->messages['resolution']['copying_fieldset_into_section'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingFieldIntoFieldset($context, $instruction) {
    $message = $context->messages['resolution']['copying_field_into_fieldset'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingFieldIntoList($context, $instruction) {
    $message = $context->messages['resolution']['copying_field_into_list'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingFieldIntoSection($context, $instruction) {
    $message = $context->messages['resolution']['copying_field_into_section'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingListIntoFieldset($context, $instruction) {
    $message = $context->messages['resolution']['copying_list_into_fieldset'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingListIntoField($context, $instruction) {
    $message = $context->messages['resolution']['copying_list_into_field'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingListIntoSection($context, $instruction) {
    $message = $context->messages['resolution']['copying_list_into_section'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingSectionIntoFieldset($context, $instruction) {
    $message = $context->messages['resolution']['copying_section_into_fieldset'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingSectionIntoField($context, $instruction) {
    $message = $context->messages['resolution']['copying_section_into_field'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingSectionIntoList($context, $instruction) {
    $message = $context->messages['resolution']['copying_section_into_list'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function copyingSectionIntoEmpty($context, $instruction) {
    $message = $context->messages['resolution']['copying_section_into_empty'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function cyclicDependency($context, $instruction, $instruction_chain) {
    $first_occurrence = array_search($instruction, $instruction_chain);
    $feedback_chain = array_slice($instruction_chain, $first_occurrence);
    $first_instruction = $feedback_chain[0];
    $last_instruction = $feedback_chain[count($feedback_chain) - 1];

    $copy_instruction = null;
    if(isset($last_instruction->template)) {
      $copy_instruction = $last_instruction;
    } else if($first_instruction->template) {
      $copy_instruction = $first_instruction;
    }

    $message = $context->messages['resolution']['cyclic_dependency'](
      $copy_instruction->line + $context->indexing,
      $copy_instruction->template
    );

    $other_instructions = array_filter($feedback_chain, function($filter_instruction) use($copy_instruction) {
      return $filter_instruction !== $copy_instruction;
    });

    $snippet = $context->reporter::report($context, $copy_instruction, $other_instructions);

    $selection = [
      [$copy_instruction->line, $copy_instruction->ranges['template'][0]],
      [$copy_instruction->line, $copy_instruction->ranges['template'][1]]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function multipleTemplatesFound($context, $instruction, $templates) {
    $message = $context->messages['resolution']['multiple_templates_found'](
      $instruction->line + $context->indexing,
      $instruction->template
    );

    $snippet = $context->reporter::report($context, array_merge([$instruction], $templates));

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  public static function templateNotFound($context, $instruction) {
    $message = $context->messages['resolution']['template_not_found'](
      $instruction->line + $context->indexing,
      $instruction->template
    );
    $snippet = $context->reporter::report($context, $instruction);
    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

}
