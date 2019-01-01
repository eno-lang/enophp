<?php declare(strict_types=1);

namespace Eno;

// Note: Study this file from the bottom up

class Grammar {
  const OPTIONAL = '([^\\n]+?)?';
  const REQUIRED = '(\\S[^\\n]*?)';

  //
  const EMPTY = '()';
  const EMPTY_LINE_INDEX = 1;

  // | Newline continuation
  const NEWLINE_CONTINUATION = '(\\|)[^\\S\\n]*'.self::OPTIONAL;
  const NEWLINE_CONTINUATION_OPERATOR_INDEX = 2;
  const NEWLINE_CONTINUATION_VALUE_INDEX = 3;

  // \ Line continuation
  const LINE_CONTINUATION = '(\\\\)[^\\S\\n]*'.self::OPTIONAL;
  const LINE_CONTINUATION_OPERATOR_INDEX = 4;
  const LINE_CONTINUATION_VALUE_INDEX = 5;

  const CONTINUATION = self::NEWLINE_CONTINUATION.'|'.self::LINE_CONTINUATION;

  // > Comment
  const COMMENT = '(>)[^\\S\\n]*'.self::OPTIONAL;
  const COMMENT_OPERATOR_INDEX = 6;
  const COMMENT_TEXT_INDEX = 7;

  // - List item value
  const LIST_ITEM = '(-)(?!-)[^\\S\\n]*'.self::OPTIONAL;
  const LIST_ITEM_OPERATOR_INDEX = 8;
  const LIST_ITEM_VALUE_INDEX = 9;

  // -- Block name
  const BLOCK = '(-{2,})[^\\S\\n]*'.self::REQUIRED;
  const BLOCK_OPERATOR_INDEX = 10;
  const BLOCK_NAME_INDEX = 11;

  // #
  const SECTION_HASHES = '(#+)(?!#)';
  const SECTION_HASHES_INDEX = 12;

  // # Section name
  const SECTION_NAME_UNESCAPED = '(?!`)([^\\s<][^<\\n]*?)';
  const SECTION_NAME_UNESCAPED_INDEX = 13;

  // # `Escaped section name`
  const SECTION_NAME_ESCAPE_BEGIN_OPERATOR_INDEX = 14;
  const SECTION_NAME_ESCAPED = '(`+)[^\\S\\n]*(\\S[^\\n]*?)[^\\S\\n]*(\\'.self::SECTION_NAME_ESCAPE_BEGIN_OPERATOR_INDEX.')'; // TODO: Should this exclude the backreference inside the quotes? (as in ((?:(?!\1).)+) ) here and elsewhere (probably not because it's not greedy.?!
  const SECTION_NAME_ESCAPED_INDEX = 15;
  const SECTION_NAME_ESCAPE_END_OPERATOR_INDEX = 16;

  // # Section name < Template name
  // # `Escaped section name` < Template name
  const SECTION_NAME = '(?:'.self::SECTION_NAME_UNESCAPED.'|'.self::SECTION_NAME_ESCAPED.')';
  const SECTION_TEMPLATE = '(?:(<(?!<)|<<)[^\\S\\n]*'.self::REQUIRED.')?';
  const SECTION = self::SECTION_HASHES.'\\s*'.self::SECTION_NAME.'[^\\S\\n]*'.self::SECTION_TEMPLATE;
  const SECTION_COPY_OPERATOR_INDEX = 17;
  const SECTION_TEMPLATE_INDEX = 18;

  const EARLY_DETERMINED = self::CONTINUATION.'|'.self::COMMENT.'|'.self::LIST_ITEM.'|'.self::BLOCK.'|'.self::SECTION;

  // Name:
  // Name: Value
  const NAME_UNESCAPED = '(?![>#\\-`\\\\|])([^\\s:=<][^:=<]*?)';
  const NAME_UNESCAPED_INDEX = 19;

  // Name:
  // `Name`: Value
  const NAME_ESCAPE_BEGIN_OPERATOR_INDEX = 20;
  const NAME_ESCAPED = '(`+)[^\\S\\n]*(\\S[^\\n]*?)[^\\S\\n]*(\\'.self::NAME_ESCAPE_BEGIN_OPERATOR_INDEX.')';
  const NAME_ESCAPED_INDEX = 21;
  const NAME_ESCAPE_END_OPERATOR_INDEX = 22;

  const NAME = '(?:'.self::NAME_UNESCAPED.'|'.self::NAME_ESCAPED.')';

  const FIELD_OR_NAME = '(:)[^\\S\\n]*'.self::OPTIONAL;
  const NAME_OPERATOR_INDEX = 23;
  const FIELD_VALUE_INDEX = 24;

  // Name of dictionary entry =
  // `Name of dictionary entry` = Value
  const FIELDSET_ENTRY = '(=)[^\\S\\n]*'.self::OPTIONAL;
  const FIELDSET_ENTRY_OPERATOR_INDEX = 25;
  const FIELDSET_ENTRY_VALUE_INDEX = 26;

  // Name < Template name
  // `Name` < Template name
  const TEMPLATE = '(<(?!<)|<<)\\s*'.self::REQUIRED;
  const COPY_OPERATOR_INDEX = 27;
  const TEMPLATE_INDEX = 28;

  const LATE_DETERMINED = self::NAME.'\\s*(?:'.self::FIELD_OR_NAME.'|'.self::FIELDSET_ENTRY.'|'.self::TEMPLATE.')';

  const NOT_EMPTY = '(?:'.self::EARLY_DETERMINED.'|'.self::LATE_DETERMINED.')';

  const REGEX = '/[^\\S\\n]*(?:'.self::EMPTY.'|'.self::NOT_EMPTY.')[^\\S\\n]*(?=\\n|$)/';
}
