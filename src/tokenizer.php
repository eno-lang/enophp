<?php

// TODO: Make extraction of comments an optional flagged feature, by default its off to gain speed!

function tokenize_error_context(&$context, $index, $line) {
  $first_instruction = null;

  while(true) {
    $end_of_line_column = strpos($context['input'], "\n", $index);

    if($end_of_line_column === false) {
      $instruction = [
        'index' => $index,
        'length' => strlen($context['input']) - $index,
        'line' => $line
      ];

      $context['instructions'][] = $instruction;

      if($first_instruction === null) {
        $first_instruction = $instruction;
      }

      return $first_instruction;
    } else {
      $instruction = [
        'index' => $index,
        'length' => $end_of_line_column - $index,
        'line' => $line
      ];

      $context['instructions'][] = $instruction;

      if($first_instruction === null) {
        $first_instruction = $instruction;
      }

      $index = $end_of_line_column + 1;
      $line++;
    }
  }
}

function tokenize(&$context)
{
  require('grammar.php');
  require('messages.php');

  $context['instructions'] = [];

  $index = 0;
  $line = 0;
  $instruction = [];

  while(true) {
    $matched = preg_match($REGEX, $context['input'], $match, PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL, $index);

    if($matched != 1 || $match[0][1] != $index) {
      // TODO: Finish port and other required ports
      // $instruction = tokenize_error_context($context, $index, $line);
      // throw errors.invalidLine(context, $instruction);
      throw new Exception('invalidLine TODO');
    }

    $instruction = [
      'index' => $index,
      'line' => $line++
    ];

    // var_dump($match);

    if(isset($match[$EMPTY_LINE_INDEX][0])) {

      $instruction['type'] = 'NOOP';

    } else if(isset($match[$NAME_OPERATOR_INDEX][0])) {

      $name_operator_column = $match[$NAME_OPERATOR_INDEX][1] - $index;

      if(isset($match[$NAME_UNESCAPED_INDEX][0])) {
        $name = $match[$NAME_UNESCAPED_INDEX][0];
        $name_column = $match[$NAME_UNESCAPED_INDEX][1] - $index;

        $instruction['name'] = $name;
        $instruction['ranges'] = [
          'name_operator' => [$name_operator_column, $name_operator_column + 1],
          'name' => [$name_column, $name_column + strlen($name)]
        ];
      } else {
        $name = $match[$NAME_ESCAPED_INDEX][0];
        $escape_operator = $match[$NAME_ESCAPE_BEGIN_OPERATOR_INDEX][0];
        $escape_begin_operator_column = $match[$NAME_ESCAPE_BEGIN_OPERATOR_INDEX][1] - $index;
        $name_column = $match[$NAME_ESCAPED_INDEX][1] - $index;
        $escape_end_operator_column = $match[$NAME_ESCAPE_END_OPERATOR_INDEX][1] - $index;

        $instruction['name'] = $name;
        $instruction['ranges'] = [
          'escape_begin_operator' => [$escape_begin_operator_column, $escape_begin_operator_column + strlen($escape_operator)],
          'escape_end_operator' => [$escape_end_operator_column, $escape_end_operator_column + strlen($escape_operator)],
          'name_operator' => [$name_operator_column, $name_operator_column + 1],
          'name' => [$name_column, $name_column + strlen($name)]
        ];
      }

      if(isset($match[$FIELD_VALUE_INDEX][0])) {
        $value = $match[$FIELD_VALUE_INDEX][0];
        $instruction['type'] = 'FIELD';
        $instruction['value'] = $value;

        $value_column = $match[$FIELD_VALUE_INDEX][1] - $index;
        $instruction['ranges']['value'] = [$value_column, $value_column + strlen($value)];
      } else {
        $instruction['type'] = 'NAME';
      }

    } else if(isset($match[$LIST_ITEM_OPERATOR_INDEX][0])) {

      $operator_column = $match[$LIST_ITEM_OPERATOR_INDEX][1] - $index;

      $instruction['ranges'] = [ 'item_operator' => [$operator_column, $operator_column + 1] ];
      $instruction['type'] = 'LIST_ITEM';
      $instruction['value'] = $match[$LIST_ITEM_VALUE_INDEX][0];

      if($instruction['value']) {
        $value_column = $match[$LIST_ITEM_VALUE_INDEX][1] - $index;
        $instruction['ranges']['value'] = [$value_column, $value_column + strlen($instruction['value'])];
      }

    } else if(isset($match[$FIELDSET_ENTRY_OPERATOR_INDEX][0])) {

      $entry_operator_column = $match[$FIELDSET_ENTRY_OPERATOR_INDEX][1] - $index;

      if(isset($match[$NAME_UNESCAPED_INDEX][0])) {
        $name = $match[$NAME_UNESCAPED_INDEX][0];
        $name_column = $match[$NAME_UNESCAPED_INDEX][1] - $index;

        $instruction['name'] = $name;
        $instruction['ranges'] = [
          'entry_operator' => [$entry_operator_column, $entry_operator_column + 1],
          'name' => [$name_column, $name_column + strlen($name)]
        ];
      } else {
        $escape_operator = $match[$NAME_ESCAPE_BEGIN_OPERATOR_INDEX][0];
        $name = $match[$NAME_ESCAPED_INDEX][0];
        $escape_begin_operator_column = $match[$NAME_ESCAPE_BEGIN_OPERATOR_INDEX][1] - $index;
        $name_column = $match[$NAME_ESCAPED_INDEX][1] - $index;
        $escape_end_operator_column = $match[$NAME_ESCAPE_END_OPERATOR_INDEX][1] - $index;

        $instruction['name'] = $name;
        $instruction['ranges'] = [
          'escape_begin_operator' => [$escape_begin_operator_column, $escape_begin_operator_column + strlen($escape_operator)],
          '$escape_end_operator' => [$escape_end_operator_column, $escape_end_operator_column + strlen($escape_operator)],
          'entry_operator' => [$entry_operator_column, $entry_operator_column + 1],
          'name' => [$name_column, $name_column + strlen($name)]
        ];
      }

      $instruction['type'] = 'FIELDSET_ENTRY';

      if(isset($match[$FIELDSET_ENTRY_VALUE_INDEX][0])) {
        $value = $match[$FIELDSET_ENTRY_VALUE_INDEX][0];
        $value_column = $match[$FIELDSET_ENTRY_VALUE_INDEX][1] - $index;
        $instruction['ranges']['value'] = [$value_column, $value_column + strlen($value)];
      }

    } else if(isset($match[$LINE_CONTINUATION_OPERATOR_INDEX][0])) {

      $operator_column = $match[$LINE_CONTINUATION_OPERATOR_INDEX][1] - $index;

      $instruction['ranges'] = [ 'line_continuation_operator' => [$operator_column, $operator_column + 1] ];
      $instruction['separator'] = ' ';
      $instruction['type'] = 'CONTINUATION';

      if(isset($match[$LINE_CONTINUATION_VALUE_INDEX][0])) {
        $value = $match[$LINE_CONTINUATION_VALUE_INDEX][0];
        $value_column = $match[$LINE_CONTINUATION_VALUE_INDEX][1] - $index;

        $instruction['value'] = $value;
        $instruction['ranges']['value'] = [$value_column, $value_column + strlen($value)];
      } else {
        $instruction['value'] = null;
      }

    } else if(isset($match[$NEWLINE_CONTINUATION_OPERATOR_INDEX][0])) {

      $operator_column = $match[$NEWLINE_CONTINUATION_OPERATOR_INDEX][1] - $index;

      $instruction['ranges'] = [ 'newline_continuation_operator' => [$operator_column, $operator_column + 1] ];
      $instruction['separator'] = "\n";
      $instruction['type'] = 'CONTINUATION';

      if(isset($match[$NEWLINE_CONTINUATION_VALUE_INDEX][0])) {
        $value = $match[$NEWLINE_CONTINUATION_VALUE_INDEX][0];
        $value_column = $match[$NEWLINE_CONTINUATION_VALUE_INDEX][1] - $index;

        $instruction['value'] = $value;
        $instruction['ranges']['value'] = [$value_column, $value_column + strlen($value)];
      } else {
        $instruction['value'] = null;
      }

    } else if(isset($match[$SECTION_HASHES_INDEX][0])) {

      $section_operator = $match[$SECTION_HASHES_INDEX][0];

      $instruction['depth'] = strlen($section_operator);
      $instruction['type'] = 'SECTION';

      $section_operator_column = $match[$SECTION_HASHES_INDEX][1] - $index;
      $name_end_column = null;

      if(isset($match[$SECTION_NAME_UNESCAPED_INDEX][0])) {
        $name = $match[$SECTION_NAME_UNESCAPED_INDEX][0];

        $name_column = $match[$SECTION_NAME_UNESCAPED_INDEX][1] - $index;
        $name_end_column = $name_column + strlen($name);

        $instruction['name'] = $name;
        $instruction['ranges'] = [
          'name' => [$name_column, $name_column + strlen($name)],
          'section_operator' => [$section_operator_column, $section_operator_column + strlen($section_operator)]
        ];
      } else {
        $name = $match[$SECTION_NAME_ESCAPED_INDEX][0];

        $escape_operator = $match[$SECTION_NAME_ESCAPE_BEGIN_OPERATOR_INDEX][0];
        $name = $match[$SECTION_NAME_ESCAPED_INDEX][0];
        $escape_begin_operator_column = $match[$SECTION_NAME_ESCAPE_BEGIN_OPERATOR_INDEX][1] - $index;
        $name_column = $match[$SECTION_NAME_ESCAPED_INDEX][1] - $index;
        $escape_end_operator_column = $match[$SECTION_NAME_ESCAPE_END_OPERATOR_INDEX][1] - $index;

        $instruction['name'] = $name;
        $instruction['ranges'] = [
          'escape_begin_operator' => [$escape_begin_operator_column, $escape_begin_operator_column + strlen($escape_operator)],
          'escape_end_operator' => [$escape_end_operator_column, $escape_end_operator_column + strlen($escape_operator)],
          'name' => [$name_column, $name_column + strlen($name)],
          'section_operator' => [$section_operator_column, $section_operator_column + strlen($section_operator)]
        ];
      }

      if(isset($match[$SECTION_TEMPLATE_INDEX][0])) {
        $template = $match[$SECTION_TEMPLATE_INDEX][0];
        $instruction['template'] = $template;

        $copy_operator = $match[$SECTION_COPY_OPERATOR_INDEX][0];
        $copy_operator_column = $match[$SECTION_COPY_OPERATOR_INDEX][1] - $index;
        $template_column = $match[$SECTION_TEMPLATE_INDEX][1] - $index;

        if($copy_operator == '<') {
          $instruction['deep_copy'] = false;
          $instruction['ranges']['copy_operator'] = [$copy_operator_column, $copy_operator_column + strlen($copy_operator)];
        } else { // copy_operator === '<<'
          $instruction['deep_copy'] = true;
          $instruction['ranges']['deep_copy_operator'] = [$copy_operator_column, $copy_operator_column + strlen($copy_operator)];
        }

        $instruction['ranges']['template'] = [$template_column, $template_column + strlen($template)];
      }

    } else if(isset($match[$BLOCK_OPERATOR_INDEX][0])) {

      $operator = $match[$BLOCK_OPERATOR_INDEX][0];
      $name = $match[$BLOCK_NAME_INDEX][0];
      $instruction['name'] = $name;
      $instruction['type'] = 'BLOCK';

      $operator_column = $match[$BLOCK_OPERATOR_INDEX][1] - $index;
      $name_column = $match[$BLOCK_NAME_INDEX][1] - $index;
      $instruction['length'] = strlen($match[0][0]);
      $instruction['ranges'] = [
        'block_operator' => [$operator_column, $operator_column + strlen($operator)],
        'name' => [$name_column, $name_column + strlen($name)]
      ];

      $index = $index + $instruction['length'] + 1;

      $context['instructions'][] = $instruction;

      $start_of_block_column = $index;

      $name_escaped = preg_quote($instruction['name']);
      $terminator_regex = "/[^\\S\\n]*(${operator})[^\\S\\n]*(${name_escaped})[^\\S\\n]*(?=\\n|$)/";

      while(true) {
        $matched = preg_match($terminator_regex, $context['input'], $terminator_match, PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL, $index);

        if($matched == 1) { // TODO: Also need to check if it matches at $index
          if($line > $instruction['line'] + 1) {
            $instruction['content_range'] = [$start_of_block_column, $index - 2];
          }

          $operator_column = $terminator_match[1][1] - $index;
          $name_column = $terminator_match[2][1] - $index;

          $instruction = [
            'index' => $index,
            'line' => $line,
            'ranges' => [
              'block_operator' => [$operator_column, $operator_column + strlen($operator)],
              'name' => [$name_column, $name_column + strlen($name)]
            ],
            'type' => 'BLOCK_TERMINATOR'
          ];

          $line++;
          // TODO: ??? matcherRegex.last_column = terminator_regex.last_column;

          break;
        } else {
          $end_of_line_column = strpos($context['input'], "\n", $index);

          if($end_of_line_column === false) {
            $context['instructions'][] = [
              'index' => $index,
              'length' => strlen($context['input']) - $index,
              'line' => $line
            ];

            // TODO: Error implementation
            throw errors.unterminatedBlock(context, instruction);
          } else {
            $context['instructions'][] = [
              'index' => $index,
              'length' => $end_of_line_column - $index,
              'line' => $line,
              'ranges' => [ 'content' => [0, $end_of_line_column - $index] ],
              'type' => 'BLOCK_CONTENT'
            ];

            $index = $end_of_line_column + 1;
            $line++;
          }
        }
      }

    } else if(isset($match[$TEMPLATE_INDEX][0])) {

      // TODO: Support for noop regular deep copy operator << probably not yet there ?

      $template = $match[$TEMPLATE_INDEX][0];
      $copy_operator_column = $match[$COPY_OPERATOR_INDEX][1] - $index;

      if(isset($match[$NAME_UNESCAPED_INDEX][0])) {
        $name = $match[$NAME_UNESCAPED_INDEX][0];

        $name_column = $match[$NAME_UNESCAPED_INDEX][1] - $index;

        $instruction['name'] = $name;
        $instruction['ranges'] = [
          'copy_operator' => [$copy_operator_column, $copy_operator_column + 1],
          'name' => [$name_column, $name_column + strlen($instruction['name'])]
        ];
      } else {
        $name = $match[$NAME_ESCAPED_INDEX][0];

        $escape_operator = $match[$NAME_ESCAPE_BEGIN_OPERATOR_INDEX][0];
        $escape_begin_operator_column = $match[$NAME_ESCAPE_BEGIN_OPERATOR_INDEX][1] - $index;
        $name_column = $match[$NAME_ESCAPED_INDEX][1] - $index;
        $escape_end_operator_column = $match[$NAME_ESCAPE_END_OPERATOR_INDEX][1] - $index;

        $instruction['name'] = $name;
        $instruction['ranges'] = [
          'copy_operator' => [$copy_operator_column, $copy_operator_column + 1],
          'escape_begin_operator' => [$escape_begin_operator_column, $escape_begin_operator_column + strlen($escape_operator)],
          'escape_end_operator' => [$escape_end_operator_column, $escape_end_operator_column + strlen($escape_operator)],
          'name' => [$name_column, $name_column + strlen($name)]
        ];
      }

      $instruction['template'] = $template;
      $instruction['type'] = 'NAME';

      $template_column = $match[$TEMPLATE_INDEX][1] - $index;
      $instruction['ranges']['template'] = [$template_column, $template_column + strlen($template)];

    } else if(isset($match[$COMMENT_OPERATOR_INDEX][0])) {

      $instruction['type'] = 'NOOP';

      $operator_column = $match[$COMMENT_OPERATOR_INDEX][1] - $index;
      $instruction['ranges'] = [ 'comment_operator' => [$operator_column, $operator_column + 1] ];

      if(isset($match[$COMMENT_TEXT_INDEX][0])) {
        $text_column = $match[$COMMENT_TEXT_INDEX][1] - $index;
        $instruction['comment'] = $match[$COMMENT_TEXT_INDEX][0];
        $instruction['ranges']['comment'] = [$text_column, $text_column + strlen($instruction['comment'])];
      }

    }

    $instruction['length'] = $match[0][1] + strlen($match[0][0]) - $index;
    $index += $instruction['length'] + 1;

    $context['instructions'][] = $instruction;

    if($index >= strlen($context['input'])) {
      if(strlen($context['input']) > 0 && $context['input'][strlen($context['input']) - 1] == "\n") {
        $context['instructions'][] = [
          'index' => strlen($context['input']),
          'length' => 0,
          'line' => $line,
          'type' => 'NOOP'
        ];
      }

      break;
    }
  } // ends while(true)
}
