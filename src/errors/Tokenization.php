<?php declare(strict_types=1);

namespace Eno\Errors;
use Eno\ParseError;
use \stdClass;

class Tokenization {
  private const UNTERMINATED_ESCAPED_NAME = "/^\s*(`+)(?!`)((?:(?!\1).)+)$/";

  // ```name: value
  static public function unterminatedEscapedName(stdClass $context, stdClass $instruction, int $unterminated_column) : ParseError {
    $line = substr($context->input, $instruction->index, $instruction->length);

    $message = $context->messages['tokenization']['unterminated_escaped_name'](
      $instruction->line + $context->indexing
    );
    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, $unterminated_column],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  static public function invalidLine(stdClass $context, stdClass $instruction) : ParseError {
    $line = substr($context->input, $instruction->index, $instruction->length);

    $matched = preg_match(self::UNTERMINATED_ESCAPED_NAME, $line, $match, PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL);;
    if($matched == 1) {
      return self::unterminatedEscapedName($context, $instruction, $match[2][1]);
    }

    $message = $context->messages['tokenization']['invalid_line'](
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report($context, $instruction);

    $selection = [
      [$instruction->line, 0],
      [$instruction->line, $instruction->length]
    ];

    return new ParseError($message, $snippet, $selection);
  }

  static public function unterminatedBlock(stdClass $context, stdClass $instruction) : ParseError {
    $block_content_instructions = array_filter($context->instructions, function($filter_instruction) use($instruction) {
      return $filter_instruction->line > $instruction->line;
    });

    $message = $context->messages['tokenization']['unterminated_block'](
      $instruction->name,
      $instruction->line + $context->indexing
    );

    $snippet = $context->reporter::report(
      $context,
      $instruction,
      $block_content_instructions
    );

    $selection = [
      [$instruction->line, $instruction->ranges['block_operator'][0]],
      [$instruction->line, $instruction->ranges['name'][1]]
    ];

    return new ParseError($message, $snippet, $selection);
  }
}
