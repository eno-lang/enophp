<?php
  // Note: Study this file from the bottom up

  $OPTIONAL = '([^\\n]+?)?';
  $REQUIRED = '(\\S[^\\n]*?)';

  //
  $EMPTY = '()';
  $EMPTY_LINE_INDEX = 1;

  // | Newline continuation
  $NEWLINE_CONTINUATION = "(\\|)[^\\S\\n]*${OPTIONAL}";
  $NEWLINE_CONTINUATION_OPERATOR_INDEX = 2;
  $NEWLINE_CONTINUATION_VALUE_INDEX = 3;

  // \ Line continuation
  $LINE_CONTINUATION = "(\\\\)[^\\S\\n]*${OPTIONAL}";
  $LINE_CONTINUATION_OPERATOR_INDEX = 4;
  $LINE_CONTINUATION_VALUE_INDEX = 5;

  $CONTINUATION = "${NEWLINE_CONTINUATION}|${LINE_CONTINUATION}";

  // > Comment
  $COMMENT = "(>)[^\\S\\n]*${OPTIONAL}";
  $COMMENT_OPERATOR_INDEX = 6;
  $COMMENT_TEXT_INDEX = 7;

  // - List item value
  $LIST_ITEM = "(-)(?!-)[^\\S\\n]*${OPTIONAL}";
  $LIST_ITEM_OPERATOR_INDEX = 8;
  $LIST_ITEM_VALUE_INDEX = 9;

  // -- Block name
  $BLOCK = "(-{2,})[^\\S\\n]*${REQUIRED}";
  $BLOCK_OPERATOR_INDEX = 10;
  $BLOCK_NAME_INDEX = 11;

  // #
  $SECTION_HASHES = '(#+)(?!#)';
  $SECTION_HASHES_INDEX = 12;

  // # Section name
  $SECTION_NAME_UNESCAPED = '(?!`)([^\\s<][^<\\n]*?)';
  $SECTION_NAME_UNESCAPED_INDEX = 13;

  // # `Escaped section name`
  $SECTION_NAME_ESCAPE_BEGIN_OPERATOR_INDEX = 14;
  $SECTION_NAME_ESCAPED = "(`+)[^\\S\\n]*(\\S[^\\n]*?)[^\\S\\n]*(\\${SECTION_NAME_ESCAPE_BEGIN_OPERATOR_INDEX})"; // TODO: Should this exclude the backreference inside the quotes? (as in ((?:(?!\1).)+) ) here and elsewhere (probably not because it's not greedy.?!
  $SECTION_NAME_ESCAPED_INDEX = 15;
  $SECTION_NAME_ESCAPE_END_OPERATOR_INDEX = 16;

  // # Section name < Template name
  // # `Escaped section name` < Template name
  $SECTION_NAME = "(?:${SECTION_NAME_UNESCAPED}|${SECTION_NAME_ESCAPED})";
  $SECTION_TEMPLATE = "(?:(<(?!<)|<<)[^\\S\\n]*${REQUIRED})?";
  $SECTION = "${SECTION_HASHES}\\s*${SECTION_NAME}[^\\S\\n]*${SECTION_TEMPLATE}";
  $SECTION_COPY_OPERATOR_INDEX = 17;
  $SECTION_TEMPLATE_INDEX = 18;

  $EARLY_DETERMINED = "${CONTINUATION}|${COMMENT}|${LIST_ITEM}|${BLOCK}|${SECTION}";

  // Name:
  // Name: Value
  $NAME_UNESCAPED = '(?![>#\\-`\\\\|])([^\\s:=<][^:=<]*?)';
  $NAME_UNESCAPED_INDEX = 19;

  // Name:
  // `Name`: Value
  $NAME_ESCAPE_BEGIN_OPERATOR_INDEX = 20;
  $NAME_ESCAPED = "(`+)[^\\S\\n]*(\\S[^\\n]*?)[^\\S\\n]*(\\${NAME_ESCAPE_BEGIN_OPERATOR_INDEX})";
  $NAME_ESCAPED_INDEX = 21;
  $NAME_ESCAPE_END_OPERATOR_INDEX = 22;

  $NAME = "(?:${NAME_UNESCAPED}|${NAME_ESCAPED})";

  $FIELD_OR_NAME = "(:)[^\\S\\n]*${OPTIONAL}";
  $NAME_OPERATOR_INDEX = 23;
  $FIELD_VALUE_INDEX = 24;

  // Name of dictionary entry =
  // `Name of dictionary entry` = Value
  $DICTIONARY_ENTRY = "(=)[^\\S\\n]*${OPTIONAL}";
  $DICTIONARY_ENTRY_OPERATOR_INDEX = 25;
  $DICTIONARY_ENTRY_VALUE_INDEX = 26;

  // Name < Template name
  // `Name` < Template name
  $COPY = "(<)\\s*${REQUIRED}";
  $COPY_OPERATOR_INDEX = 27;
  $TEMPLATE_INDEX = 28;

  $LATE_DETERMINED = "${NAME}\\s*(?:${FIELD_OR_NAME}|${DICTIONARY_ENTRY}|${COPY})";

  $NOT_EMPTY = "(?:${EARLY_DETERMINED}|${LATE_DETERMINED})";

  $REGEX = "/[^\\S\\n]*(?:${EMPTY}|${NOT_EMPTY})[^\\S\\n]*(?=\\n|$)/";
?>
