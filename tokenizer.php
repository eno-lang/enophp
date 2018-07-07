<?php

// TODO: Make extraction of comments an optional flagged feature, by default its off to gain speed!

// function tokenize_error_context($context, $index, $line) {
//   $first_instruction = null;
//
//   while(true) {
//     $endOfLine_index = $context['input'].indexOf("\n", index);
//
//     if(endOfLine_index === -1) {
//       $instruction = [
//         index: index,
//         length: $context['input'].length - $index,
//         line: line
//       ];
//
//       context['instructions'].push(instruction);
//
//       if(!first_instruction) {
//         first_instruction = instruction;
//       }
//
//       return first_instruction;
//     } else {
//       $instruction = [
//         index: index,
//         length: endOfLine_index - $index,
//         line: line
//       ];
//
//       context['instructions'].push(instruction);
//
//       if(!first_instruction) {
//         first_instruction = instruction;
//       }
//
//       index = endOfLine_index + 1;
//       line++;
//     }
//   }
// ];

function tokenize(&$context)
{
  require_once('grammar.php');
  require_once('messages.php');

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

      $unescaped_name = $match[$NAME_UNESCAPED_INDEX][0];
      $name_operator_index = null;

      if($unescaped_name) {
        $instruction['name'] = $unescaped_name;

        $name_index = $match[$NAME_UNESCAPED_INDEX][1];
        $name_operator_index = $match[$NAME_OPERATOR_INDEX][1];

        $instruction['ranges'] = [
          'name_operator' => [$name_operator_index - $index, $name_operator_index - $index + 1],
          'name' => [$name_index - $index, $name_index - $index + strlen($instruction['name'])]
        ];
      } else {
        $instruction['name'] = $match[$NAME_ESCAPED_INDEX][0];

        $escape_operator = $match[$NAME_ESCAPE_BEGIN_OPERATOR_INDEX][0];
        $escape_begin_operator_ndex = $match[$NAME_ESCAPE_BEGIN_OPERATOR_INDEX][1];
        $name_index = $match[$NAME_ESCAPED_INDEX][1];
        $escape_end_operator_index = $match[$NAME_ESCAPE_END_OPERATOR_INDEX][1];
        $name_operator_index = $match[$NAME_OPERATOR_INDEX][1];

        $instruction['ranges'] = [
          'escape_begin_operator' => [$escape_begin_operator_ndex - $index, $escape_begin_operator_ndex - $index + strlen($escape_operator)],
          'escape_end_operator' => [$escape_end_operator_index - $index, $escape_end_operator_index - $index + strlen($escape_operator)],
          'name_operator' => [$name_operator_index - $index, $name_operator_index - $index + 1],
          'name' => [$name_index - $index, $name_index - $index + strlen($instruction['name'])]
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


      $instruction['type'] = 'LIST_ITEM';
      $instruction['value'] = $match[$LIST_ITEM_VALUE_INDEX][0];

      $operator_column = $match[$LIST_ITEM_OPERATOR_INDEX][1] - $index;
      $instruction['ranges'] = [ 'item_operator' => [$operator_column, $operator_column + 1] ];

      if($instruction['value']) {
        $value_column = $match[$LIST_ITEM_VALUE_INDEX][1] - $index;
        $instruction['ranges']['value'] = [$value_column, $value_column + strlen($instruction['value'])];
      }


    // } else if($match[$DICTIONARY_ENTRY_OPERATOR_INDEX]) {
    //
    //
    //   $unescaped_name = $match[$NAME_UNESCAPED_INDEX];
    //   let entry_operator_index;
    //
    //   if(unescaped_name) {
    //     $instruction['name'] = unescaped_name;
    //
    //     $name_index = $context['input'].indexOf(instruction['name'], index);
    //     entry_operator_index = $context['input'].indexOf('=', name_index + strlen($instruction['name']));
    //
    //     $instruction['ranges'] = [
    //       'entry_operator' => [entry_operator_index - $index, entry_operator_index - $index + 1],
    //       name: [name_index - $index, name_index - $index + strlen($instruction['name'])]
    //     ];
    //   } else {
    //     $instruction['name'] = $match[$NAME_ESCAPED_INDEX];
    //
    //     $escape_operator = $match[$NAME_ESCAPE_BEGIN_OPERATOR_INDEX];
    //     $escape_begin_operator_ndex = $context['input'].indexOf(escape_operator, index);
    //     $name_index = $context['input'].indexOf(instruction['name'], escape_begin_operator_ndex + escape_operator.length);
    //     $escape_end_operator_index = $context['input'].indexOf(escape_operator, name_index + strlen($instruction['name']));
    //     entry_operator_index = $context['input'].indexOf('=', escape_end_operator_index + escape_operator.length);
    //
    //     $instruction['ranges'] = [
    //       'escape_begin_operator' => [escape_begin_operator_ndex - $index, escape_begin_operator_ndex - $index + escape_operator.length],
    //       'escape_end_operator' => [escape_end_operator_index - $index, escape_end_operator_index - $index + escape_operator.length],
    //       'entry_operator' => [entry_operator_index - $index, entry_operator_index - $index + 1],
    //       name: [name_index - $index, name_index - $index + strlen($instruction['name'])]
    //     ];
    //   }
    //
    //   $instruction['type'] = 'DICTIONARY_ENTRY';
    //   $instruction['value'] = $match[$DICTIONARY_ENTRY_VALUE_INDEX] || null;
    //
    //   if($instruction['value']) {
    //     $value_index = $context['input'].indexOf($instruction['value'], entry_operator_index + 1);
    //     $instruction['ranges']['value'] = [value_index - $index, value_index - $index + strlen($instruction['value'])];
    //   }
    //
    //
    } else if(isset($match[$LINE_CONTINUATION_OPERATOR_INDEX][0])) {

      $instruction['separator'] = ' ';
      $instruction['type'] = 'CONTINUATION';
      $instruction['value'] = $match[$LINE_CONTINUATION_VALUE_INDEX][0];

      $operator_column = $match[$LINE_CONTINUATION_OPERATOR_INDEX][1] - $index;
      $instruction['ranges'] = [ 'line_continuation_operator' => [$operator_column, $operator_column + 1] ];

      if($instruction['value']) {
        $value_column = $match[$LINE_CONTINUATION_VALUE_INDEX][1] - $index;
        $instruction['ranges']['value'] = [$value_column, $value_column + strlen($instruction['value'])];
      }

    } else if(isset($match[$NEWLINE_CONTINUATION_OPERATOR_INDEX][0])) {

      $instruction['separator'] = "\n";
      $instruction['type'] = 'CONTINUATION';
      $instruction['value'] = $match[$NEWLINE_CONTINUATION_VALUE_INDEX][0];

      $operator_column = $match[$NEWLINE_CONTINUATION_OPERATOR_INDEX][1] - $index;
      $instruction['ranges'] = [ 'newline_continuation_operator' => [$operator_column, $operator_column + 1] ];

      if($instruction['value']) {
        $value_column = $match[$NEWLINE_CONTINUATION_VALUE_INDEX][1] - $index;
        $instruction['ranges']['value'] = [$value_column, $value_column + strlen($instruction['value'])];
      }

    // } else if($match[$SECTION_HASHES_INDEX]) {
    //
    //
    //   $section_operator = $match[$SECTION_HASHES_INDEX];
    //
    //   instruction.depth = section_operator.length;
    //   $instruction['type'] = 'SECTION';
    //
    //   $section_operator_index = $context['input'].indexOf(section_operator, index);
    //   $unescaped_name = $match[$SECTION_NAME_UNESCAPED_INDEX];
    //   let nameEnd_index;
    //
    //   if(unescaped_name) {
    //     $instruction['name'] = unescaped_name;
    //
    //     $name_index = $context['input'].indexOf(instruction['name'], section_operator_index + section_operator.length);
    //     nameEnd_index = name_index + unescaped_name.length;
    //
    //     $instruction['ranges'] = [
    //       name: [name_index - $index, name_index - $index + unescaped_name.length],
    //       'section_operator' => [section_operator_index - $index, section_operator_index - $index + section_operator.length]
    //     ];
    //   } else {
    //     $instruction['name'] = $match[$SECTION_NAME_ESCAPED_INDEX];
    //
    //     $escape_operator = $match[$SECTION_NAME_ESCAPE_BEGIN_OPERATOR_INDEX];
    //     $escape_begin_operator_ndex = $context['input'].indexOf(escape_operator, section_operator_index + section_operator.length);
    //     $name_index = $context['input'].indexOf(instruction['name'], escape_begin_operator_ndex + escape_operator.length);
    //     $escape_end_operator_index = $context['input'].indexOf(escape_operator, name_index + strlen($instruction['name']));
    //     nameEnd_index = escape_end_operator_index + escape_operator.length;
    //
    //     $instruction['ranges'] = [
    //       'escape_begin_operator' => [escape_begin_operator_ndex - $index, escape_begin_operator_ndex - $index + escape_operator.length],
    //       'escape_end_operator' => [escape_end_operator_index - $index, escape_end_operator_index - $index + escape_operator.length],
    //       name: [name_index - $index, name_index - $index + strlen($instruction['name'])],
    //       'section_operator' => [section_operator_index - $index, section_operator_index - $index + section_operator.length]
    //     ];
    //   }
    //
    //   $template = $match[$SECTION_TEMPLATE_INDEX];
    //   if(template) {
    //     instruction.template = template;
    //
    //     $copy_operator = $match[$SECTION_COPY_OPERATOR_INDEX];
    //     $copy_operator_index = $context['input'].indexOf(copy_operator, nameEnd_index);
    //     $template_index = $context['input'].indexOf(template, copy_operator_index + copy_operator.length);
    //
    //     if(copy_operator === '<') {
    //       instruction.deepCopy = false;
    //       $instruction['ranges'].copy_operator = [copy_operator_index - $index, copy_operator_index - $index + copy_operator.length];
    //     } else { // copy_operator === '<<'
    //       instruction.deepCopy = true;
    //       $instruction['ranges'].deepCopy_operator = [copy_operator_index - $index, copy_operator_index - $index + copy_operator.length];
    //     }
    //
    //     $instruction['ranges'].template = [template_index - $index, template_index - $index + template.length];
    //   }
    //
    //
    // } else if($match[$BLOCK_DASHES_INDEX]) {
    //
    //
    //   $blockDashes = $match[$BLOCK_DASHES_INDEX];
    //   $instruction['name'] = $match[$BLOCK_NAME_INDEX];
    //   $instruction['type'] = 'BLOCK';
    //
    //   let operator_index = $context['input'].indexOf(blockDashes, index);
    //   let name_index = $context['input'].indexOf(instruction['name'], operator_index + blockDashes.length);
    //   instruction.length = matcherRegex.last_index - $index;
    //   $instruction['ranges'] = [
    //     'block_operator' => [operator_index - $index, operator_index - $index + blockDashes.length],
    //     name: [name_index - $index, name_index - $index + strlen($instruction['name'])]
    //   ];
    //
    //   index = matcherRegex.last_index + 1;
    //
    //   context['instructions'].push(instruction);
    //
    //   $startOfBlock_index = index;
    //
    //   $nameEscaped = instruction['name'].replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
    //   $terminatorMatcher = new RegExp(`[^\\S\\n]*(${blockDashes})[^\\S\\n]*(${nameEscaped})[^\\S\\n]*(?=\\n|$)`, 'y');
    //
    //   while(true) {
    //     terminatorMatcher.last_index = index;
    //     let terminatorMatch = terminatorMatcher.exec(context.input);
    //
    //     if(terminatorMatch) {
    //       if(line > instruction.line + 1) {
    //         instruction.contentRange = [startOfBlock_index, index - 2];
    //       }
    //
    //       operator_index = $context['input'].indexOf(blockDashes, index);
    //       name_index = $context['input'].indexOf(instruction['name'], operator_index + blockDashes.length);
    //
    //       instruction = [
    //         index: index,
    //         'line' => $line,
    //         ranges: {
    //           'block_operator' => [operator_index - $index, operator_index - $index + blockDashes.length],
    //           name: [name_index - $index, name_index - $index + strlen($instruction['name'])]
    //         },
    //         type: 'BLOCK_TERMINATOR'
    //       ];
    //
    //       line++;
    //       matcherRegex.last_index = terminatorMatcher.last_index;
    //
    //       break;
    //     } else {
    //       $endofLine_index = $context['input'].indexOf("\n", index);
    //
    //       if(endofLine_index === -1) {
    //         context['instructions'].push({
    //           index: index,
    //           length: $context['input'].length - $index,
    //           line: line
    //         });
    //
    //         throw errors.unterminatedBlock(context, instruction);
    //       } else {
    //         context['instructions'].push({
    //           index: index,
    //           length: endofLine_index - $index,
    //           'line' => $line,
    //           ranges: { content: [0, endofLine_index - $index] },
    //           type: 'BLOCK_CONTENT'
    //         });
    //
    //         index = endofLine_index + 1;
    //         line++;
    //       }
    //     }
    //   }
    //
    //
    // } else if($match[$TEMPLATE_INDEX]) {
    //
    //
    //   // TODO: Support for noop regular deep copy operator << probably not yet there ?
    //
    //   $template = $match[$TEMPLATE_INDEX];
    //   $unescaped_name = $match[$NAME_UNESCAPED_INDEX];
    //   let copy_operator_index;
    //
    //   if(unescaped_name) {
    //     $instruction['name'] = unescaped_name;
    //
    //     $name_index = $context['input'].indexOf(instruction['name'], index);
    //     copy_operator_index = $context['input'].indexOf('<', name_index + strlen($instruction['name']));
    //
    //     $instruction['ranges'] = [
    //       'copy_operator' => [copy_operator_index - $index, copy_operator_index - $index + 1],
    //       name: [name_index - $index, name_index - $index + strlen($instruction['name'])]
    //     ];
    //   } else {
    //     $instruction['name'] = $match[$NAME_ESCAPED_INDEX];
    //
    //     $escape_operator = $match[$NAME_ESCAPE_BEGIN_OPERATOR_INDEX];
    //     $escape_begin_operator_ndex = $context['input'].indexOf(escape_operator, index);
    //     $name_index = $context['input'].indexOf(instruction['name'], escape_begin_operator_ndex + escape_operator.length);
    //     $escape_end_operator_index = $context['input'].indexOf(escape_operator, name_index + strlen($instruction['name']));
    //     copy_operator_index = $context['input'].indexOf('<', escape_end_operator_index + escape_operator.length);
    //
    //     $instruction['ranges'] = [
    //       'copy_operator' => [copy_operator_index - $index, copy_operator_index - $index + 1],
    //       'escape_begin_operator' => [escape_begin_operator_ndex - $index, escape_begin_operator_ndex - $index + escape_operator.length],
    //       'escape_end_operator' => [escape_end_operator_index - $index, escape_end_operator_index - $index + escape_operator.length],
    //       name: [name_index - $index, name_index - $index + strlen($instruction['name'])]
    //     ];
    //   }
    //
    //   instruction.template = template;
    //   $instruction['type'] = 'NAME';
    //
    //   $template_index = $context['input'].indexOf(template, copy_operator_index + 1);
    //   $instruction['ranges'].template = [template_index - $index, template_index - $index + template.length];


    } else if(isset($match[$COMMENT_OPERATOR_INDEX][0])) {

      $instruction['type'] = 'NOOP';

      $operator_column = $match[$COMMENT_OPERATOR_INDEX][1] - $index;
      $instruction['ranges'] = [ 'comment_operator' => [$operator_column, $operator_column + 1] ];

      if($match[$COMMENT_TEXT_INDEX][0] != null) {
        $text_column = $match[$COMMENT_TEXT_INDEX][1] - $index;
        $instruction['comment'] = $match[$COMMENT_TEXT_INDEX][0];
        $instruction['ranges']['comment'] = [$text_column, $text_column + strlen($instruction['comment'])];
      }

    }

    $instruction['length'] = $match[0][1] + strlen($match[0][0]) - $index;
    $index += $instruction['length'] + 1;

    $context['instructions'][] = $instruction;

    if($index >= strlen($context['input'])) {
      if($context['input'][strlen($context['input']) - 1] == "\n") {
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

?>
