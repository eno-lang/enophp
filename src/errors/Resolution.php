<?php declare(strict_types=1);

namespace Eno\Errors;
use Eno\ParseError;
use \stdClass;

class Resolution {
  public static function copyingBlockIntoFieldset(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingBlockIntoList(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingBlockIntoSection(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingFieldsetIntoField(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingFieldsetIntoList(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingFieldsetIntoSection(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingFieldIntoFieldset(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingFieldIntoList(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingFieldIntoSection(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingListIntoFieldset(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingListIntoField(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingListIntoSection(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingSectionIntoFieldset(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingSectionIntoField(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingSectionIntoList(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function copyingSectionIntoEmpty(stdClass $context, stdClass $instruction) : ParseError {
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

  public static function cyclicDependency(stdClass $context, stdClass $instruction, array $instruction_chain) : ParseError {
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

  public static function multipleTemplatesFound(stdClass $context, stdClass $instruction, array $templates) : ParseError {
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

  public static function templateNotFound(stdClass $context, stdClass $instruction) : ParseError {
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
