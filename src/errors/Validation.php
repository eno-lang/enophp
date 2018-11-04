<?php declare(strict_types=1);

namespace Eno\Errors;
use Eno\ValidationError;
use \stdClass;

function deepExpandInstruction(stdClass $instruction) : array {
  $result = [$instruction];

  if(array_key_exists('subinstructions', $instruction)) {
    foreach($instruction->subinstructions as $subinstruction) {
      $result = array_merge($result, deepExpandInstruction($subinstruction));
    }
  }

  return $result;
}

function expandInstructions(array $instructions) : array {
  $result = [];

  foreach($instructions as $instruction) {
    $result[] = $instruction;

    if(array_key_exists('subinstructions', $instruction)) {
      $result = array_merge($result, $instruction->subinstructions);
    }
  }

  return $result;
}

class Validation {
  public static function exactCountNotMet(stdClass $context, stdClass $instruction, int $exact_count) : ValidationError {
    $message = $context->messages['validation']['exact_count_not_met'](
      $instruction->name,
      array_key_exists('subinstructions', $instruction) ? count($instruction->subinstructions) : 0,
      $exact_count
    );

    $selection = null;
    $snippet = null;
    if(array_key_exists('subinstructions', $instruction) && count($instruction->subinstructions) > 0) {
      $last_subinstruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection = [
        [$instruction->subinstructions[0]->line, 0],
        [$last_subinstruction->line, $last_subinstruction->length]
      ];
      $snippet = $context->reporter::report($context, $instruction->subinstructions, $instruction);
    } else {
      $selection = [
        [$instruction->line, $instruction->length],
        [$instruction->line, $instruction->length]
      ];
      $snippet = $context->reporter::report($context, $instruction);
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function excessName(stdClass $context, ?string $message, stdClass $instruction) : ValidationError {
    if($message === null) {
      $message = $context->messages['validation']['excess_name']($instruction->name);
    }

    $snippet = null;
    $selection = [[$instruction->line, 0]];
    if(array_key_exists('subinstructions', $instruction) && count($instruction->subinstructions) > 0) {
      $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $snippet = $context->reporter::report($context, $instruction);
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldsetsGotField(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_fieldsets_got_field']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldsetsGotList(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_fieldsets_got_list']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldsetsGotSection(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_fieldsets_got_section']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldsetGotFieldsets(stdClass $context, string $name, array $instructions) : ValidationError {
    $expanded_instructions = expandInstructions($instructions);
    $last_instruction = $expanded_instructions[count($expanded_instructions) - 1];

    $message = $context->messages['validation']['expected_fieldset_got_fieldsets']($name);

    $snippet = $context->reporter::report($context, $instructions, $expanded_instructions);

    $selection = [
      [$expanded_instructions[0]->line, 0],
      [$last_instruction->line, $last_instruction->length]
    ];

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldsetGotField(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_fieldset_got_field']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldsetGotList(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_fieldset_got_list']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldsetGotSection(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_fieldset_got_section']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedElementGotElements(stdClass $context, string $name, array $instructions) : ValidationError {
    $expanded_instructions = expandInstructions($instructions);
    $last_instruction = $expanded_instructions[count($expanded_instructions) - 1];

    $message = $context->messages['validation']['expected_element_got_elements']($name);

    $snippet = $context->reporter::report($context, $instructions, $expanded_instructions);

    $selection = [
      [$expanded_instructions[0]->line, 0],
      [$last_instruction->line, $last_instruction->length]
    ];

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldGotFieldset(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_field_got_fieldset']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldGotList(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_field_got_list']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldGotFields(stdClass $context, string $name, array $instructions) : ValidationError {
    $expanded_instructions = expandInstructions($instructions);
    $last_instruction = $expanded_instructions[count($expanded_instructions) - 1];

    $message = $context->messages['validation']['expected_field_got_fields']($name);

    $snippet = $context->reporter::report($context, $instructions, $expanded_instructions);

    $selection = [
      [$expanded_instructions[0]->line, 0],
      [$last_instruction->line, $last_instruction->length]
    ];

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldGotSection(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_field_got_section']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldsGotFieldset(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_fields_got_fieldset']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldsGotList(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_fields_got_list']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedFieldsGotSection(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_fields_got_section']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedListGotFieldset(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_list_got_fieldset']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedListGotField(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_list_got_field']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedListGotLists(stdClass $context, string $name, array $instructions) : ValidationError {
    $expanded_instructions = expandInstructions($instructions);
    $last_instruction = $expanded_instructions[count($expanded_instructions) - 1];

    $message = $context->messages['validation']['expected_list_got_lists']($name);

    $snippet = $context->reporter::report($context, $instructions, $expanded_instructions);

    $selection = [
      [$expanded_instructions[0]->line, 0],
      [$last_instruction->line, $last_instruction->length]
    ];

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedListGotSection(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_list_got_section']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedListsGotFieldset(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_lists_got_fieldset']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedListsGotField(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_lists_got_field']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedListsGotSection(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_lists_got_section']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedSectionGotFieldset(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_section_got_fieldset']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedSectionGotEmpty(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_section_got_empty']($instruction->name);

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedSectionGotField(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_section_got_field']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedSectionGotList(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_section_got_list']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedSectionGotSections(stdClass $context, string $name, array $instructions) : ValidationError {
    $expanded_instructions = expandInstructions($instructions);
    $last_instruction = $expanded_instructions[count($expanded_instructions) - 1];

    $message = $context->messages['validation']['expected_section_got_sections']($name);

    $snippet = $context->reporter::report($context, $instructions, $expanded_instructions);

    $selection = [
      [$expanded_instructions[0]->line, 0],
      [$last_instruction->line, $last_instruction->length]
    ];

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedSectionsGotFieldset(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_sections_got_fieldset']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedSectionsGotEmpty(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_sections_got_empty']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedSectionsGotField(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_sections_got_field']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function expectedSectionsGotList(stdClass $context, stdClass $instruction) : ValidationError {
    $message = $context->messages['validation']['expected_sections_got_list']($instruction->name);

    $snippet = $context->reporter::report($context, array_merge([$instruction], $instruction->subinstructions));

    $selection = [[$instruction->line, 0]];
    if(count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function maxCountNotMet(stdClass $context, stdClass $instruction, int $max_count) : ValidationError {
    $message = $context->messages['validation']['max_count_not_met'](
      $instruction->name,
      array_key_exists('subinstructions', $instruction) ? count($instruction->subinstructions) : 0,
      $max_count
    );

    $selection = null;
    $snippet = null;
    if(array_key_exists('subinstructions', $instruction) && count($instruction->subinstructions) > 0) {
      $last_subinstruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection = [
        [$instruction->subinstructions[0]->line, 0],
        [$last_subinstruction->line, $last_subinstruction->length]
      ];
      $snippet = $context->reporter::report($context, $instruction->subinstructions, $instruction);
    } else {
      $selection = [
        [$instruction->line, $instruction->length],
        [$instruction->line, $instruction->length]
      ];
      $snippet = $context->reporter::report($context, $instruction);
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function minCountNotMet(stdClass $context, stdClass $instruction, int $min_count) : ValidationError {
    $message = $context->messages['validation']['min_count_not_met'](
      $instruction->name,
      array_key_exists('subinstructions', $instruction) ? count($instruction->subinstructions) : 0,
      $min_count
    );

    $selection = null;
    $snippet = null;
    if(array_key_exists('subinstructions', $instruction) && count($instruction->subinstructions) > 0) {
      $last_subinstruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection = [
        [$instruction->subinstructions[0]->line, 0],
        [$last_subinstruction->line, $last_subinstruction->length]
      ];
      $snippet = $context->reporter::report($context, $instruction->subinstructions, $instruction);
    } else {
      $selection = [
        [$instruction->line, $instruction->length],
        [$instruction->line, $instruction->length]
      ];
      $snippet = $context->reporter::report($context, $instruction);
    }

    return new ValidationError($message, $snippet, $selection);
  }

  // TODO: Exclude sections within sections for all the missing* errors (except missingFieldsetEntry)

  public static function missingFieldset(stdClass $context, string $name, stdClass $section_instruction) : ValidationError {
    $message = $context->messages['validation']['missing_fieldset']($name);

    $snippet = $context->reporter::report($context, $section_instruction, deepExpandInstruction($section_instruction));

    $selection = [
      [$section_instruction->line, $section_instruction->length],
      [$section_instruction->line, $section_instruction->length]
    ];

    return new ValidationError($message, $snippet, $selection);
  }

  public static function missingFieldsetEntry(stdClass $context, string $name, stdClass $fieldset_instruction) : ValidationError {
    $message = $context->messages['validation']['missing_fieldset_entry']($name);

    $snippet = $context->reporter::report($context, $fieldset_instruction, deepExpandInstruction($fieldset_instruction));

    $selection = [
      [$fieldset_instruction->line, $fieldset_instruction->length],
      [$fieldset_instruction->line, $fieldset_instruction->length]
    ];

    return new ValidationError($message, $snippet, $selection);
  }

  public static function missingElement(stdClass $context, string $name, stdClass $section_instruction) : ValidationError {
    $message = $context->messages['validation']['missing_element']($name);

    $snippet = $context->reporter::report($context, $section_instruction, deepExpandInstruction($section_instruction));

    $selection = [
      [$section_instruction->line, $section_instruction->length],
      [$section_instruction->line, $section_instruction->length]
    ];

    return new ValidationError($message, $snippet, $selection);
  }

  public static function missingField(stdClass $context, string $name, stdClass $section_instruction) : ValidationError {
    $message = $context->messages['validation']['missing_field']($name);

    $snippet = $context->reporter::report($context, $section_instruction, deepExpandInstruction($section_instruction));

    $selection = [
      [$section_instruction->line, $section_instruction->length],
      [$section_instruction->line, $section_instruction->length]
    ];

    return new ValidationError($message, $snippet, $selection);
  }

  public static function missingList(stdClass $context, string $name, stdClass $section_instruction) : ValidationError {
    $message = $context->messages['validation']['missing_list']($name);

    $snippet = $context->reporter::report($context, $section_instruction, deepExpandInstruction($section_instruction));

    $selection = [
      [$section_instruction->line, $section_instruction->length],
      [$section_instruction->line, $section_instruction->length]
    ];

    return new ValidationError($message, $snippet, $selection);
  }

  public static function missingSection(stdClass $context, string $name, stdClass $section_instruction) : ValidationError {
    $message = $context->messages['validation']['missing_section']($name);

    $snippet = $context->reporter::report($context, $section_instruction, deepExpandInstruction($section_instruction));

    $selection = [
      [$section_instruction->line, $section_instruction->length],
      [$section_instruction->line, $section_instruction->length]
    ];

    return new ValidationError($message, $snippet, $selection);
  }

  // TODO: Revisit and polish the two core value errors again at some point (missingValue / valueError)
  //       (In terms of quality of results and architecture - DRY up probably)
  //       Share best implementation among other eno libraries

  public static function missingValue(stdClass $context, stdClass $instruction) : ValidationError {
    $message = null;
    $selection = null;

    if($instruction->type == 'FIELD' || $instruction->type == 'NAME' || $instruction->type == 'BLOCK') {
      $message = $context->messages['validation']['missing_field_value']($instruction->name);

      if(array_key_exists('template', $instruction->ranges)) {
        $selection = [[$instruction->line, $instruction->ranges['template'][1]]];
      } else if(array_key_exists('name_operator', $instruction->ranges)) {
        $selection = [[
          $instruction->line,
          min($instruction->ranges['name_operator'][1] + 1, $instruction->length)
        ]];
      } else {
        $selection = [[$instruction->line, $instruction->length]];
      }
    } else if($instruction->type == 'FIELDSET_ENTRY') {
      $message = $context->messages['validation']['missing_fieldset_entry_value']($instruction->name);
      $selection = [[
        $instruction->line,
        min($instruction->ranges['entry_operator'][1] + 1, $instruction->length)
      ]];
    } else if($instruction->type == 'LIST_ITEM') {
      $message = $context->messages['validation']['missing_list_item_value']($instruction->name);
      $selection = [[
        $instruction->line,
        min($instruction->ranges['item_operator'][1] + 1, $instruction->length)
      ]];
    }

    $snippet = $context->reporter::report($context, $instruction, deepExpandInstruction($instruction));

    if($instruction->type != 'BLOCK' &&
       array_key_exists('subinstructions', $instruction) && count($instruction->subinstructions) > 0) {
      $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
      $selection[] = [$last_instruction->line, $last_instruction->length];
    } else {
      $selection[] = [$instruction->line, $instruction->length];
    }

    return new ValidationError($message, $snippet, $selection);
  }

  public static function valueError(stdClass $context, ?string $message, stdClass $instruction) : ValidationError {
    if($message === null) {
      $message = $context->messages['validation']['generic_error']($instruction->name);
    }

    $snippet = null;
    $selection = null;

    if($instruction->type == 'BLOCK') {
      $content_instructions = array_filter(
        function($instruction) { return $instruction->type == 'BLOCK_CONTENT'; },
        $instruction->subinstructions
      );
      $terminator_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];

      if(count($content_instructions) > 0) {
        $first_instruction = $content_instructions[0];
        $last_instruction = $content_instructions[count($content_instructions) - 1];

        $snippet = $context->reporter::report($context, $content_instructions);
        $selection = [
          [$first_instruction->line, $first_instruction->ranges['content'][0]],
          [$last_instruction->line, $last_instruction->ranges['content'][1]]
        ];
      } else {
        $snippet = $context->reporter::report($context, [$instruction, $terminator_instruction]);
        $selection = [
          [$instruction->line, $instruction->length],
          [$instruction->line, $instruction->length]
        ];
      }
    } else {
      $snippet = $context->reporter::report($context, deepExpandInstruction($instruction));

      if(array_key_exists('value', $instruction->ranges)) {
        $selection = [[$instruction->line, $instruction->ranges['value'][0]]];
      } else if(array_key_exists('template', $instruction->ranges)) {
        $selection = [[$instruction->line, $instruction->ranges['template_operator'][0]]];
      } else if(array_key_exists('name_operator', $instruction->ranges)) {
        $selection = [[
          $instruction->line,
          min($instruction->ranges['name_operator'][1] + 1, $instruction->length)
        ]];
      } else if(array_key_exists('entry_operator', $instruction->ranges)) {
        $selection = [[
          $instruction->line,
          min($instruction->ranges['entry_operator'][1] + 1, $instruction->length)
        ]];
      } else if($instruction->type == 'LIST_ITEM') {
        $selection = [[
          $instruction->line,
          min($instruction->ranges['item_operator'][1] + 1, $instruction->length)
        ]];
      } else {
        $selection = [[$instruction->line, $instruction->length]];
      }

      if(array_key_exists('subinstructions', $instruction) && count($instruction->subinstructions) > 0) {
        $last_instruction = $instruction->subinstructions[count($instruction->subinstructions) - 1];
        $selection[] = [$last_instruction->line, $last_instruction->length];
      } else {
        if(array_key_exists('value', $instruction->ranges)) {
          $selection[] = [$instruction->line, $instruction->ranges['value'][1]];
        } else {
          $selection[] = [$instruction->line, $instruction->length];
        }
      }
    }

    return new ValidationError($message, $snippet, $selection);
  }
}
