<?php

namespace Eno\Errors;
use Eno\ValidationError;

function deepExpandInstruction($instruction) {
  $result = [$instruction];

  if(array_key_exists('subinstructions', $instruction)) {
    foreach($instruction->subinstructions as $subinstruction) {
      $result = array_merge($result, deepExpandInstruction($subinstruction));
    }
  }

  return $result;
}

class Validation {
  public static function valueError($context, $message, $instruction) {
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

      if($instruction->subinstructions && count($instruction->subinstructions) > 0) {
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
