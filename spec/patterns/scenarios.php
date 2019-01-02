<?php declare(strict_types=1);

use Eno\Grammar;

require_once(__DIR__ . '/util.php');

$SCENARIOS = [

  // NEWLINE_CONTINUATION_SCENARIOS
  [
    'captures' => [
      Grammar::NEWLINE_CONTINUATION_OPERATOR_INDEX => '|'
    ],
    'syntax' => '|',
    'variants' => space('|')
  ],
  [
    'captures' => [
      Grammar::NEWLINE_CONTINUATION_OPERATOR_INDEX => '|',
      Grammar::NEWLINE_CONTINUATION_VALUE_INDEX => 'Value'
    ],
    'syntax' => '| Value',
    'variants' => space('|', 'Value')
  ],
  [
    'captures' => [
      Grammar::NEWLINE_CONTINUATION_OPERATOR_INDEX => '|',
      Grammar::NEWLINE_CONTINUATION_VALUE_INDEX => '|'
    ],
    'syntax' => '| |',
    'variants' => space('|', '|')
  ],

  // LINE_CONTINUATION_SCENARIOS
  [
    'captures' => [
      Grammar::LINE_CONTINUATION_OPERATOR_INDEX => '\\'
    ],
    'syntax' => '\\',
    'variants' => space('\\')
  ],
  [
    'captures' => [
      Grammar::LINE_CONTINUATION_OPERATOR_INDEX => '\\',
      Grammar::LINE_CONTINUATION_VALUE_INDEX => 'Value'
    ],
    'syntax' => '\\ Value',
    'variants' => space('\\', 'Value')
  ],
  [
    'captures' => [
      Grammar::LINE_CONTINUATION_OPERATOR_INDEX => '\\',
      Grammar::LINE_CONTINUATION_VALUE_INDEX => '\\'
    ],
    'syntax' => '\\ \\',
    'variants' => space('\\', '\\')
  ],

  // BLOCK_SCENARIOS
  [
    'captures' => [
      Grammar::BLOCK_OPERATOR_INDEX => '--',
      Grammar::BLOCK_NAME_INDEX => 'Name'
    ],
    'syntax' => '-- Name',
    'variants' => space('--', 'Name')
  ],
  [
    'captures' => [
      Grammar::BLOCK_OPERATOR_INDEX => '--',
      Grammar::BLOCK_NAME_INDEX => '--'
    ],
    'syntax' => '-- --',
    'variants' => space('--', ' ', '--')
  ],
  [
    'captures' => [
      Grammar::BLOCK_OPERATOR_INDEX => '---',
      Grammar::BLOCK_NAME_INDEX => 'The Name'
    ],
    'syntax' => '--- The Name',
    'variants' => space('---', 'The Name')
  ],
  [
    'captures' => [
      Grammar::BLOCK_OPERATOR_INDEX => '---',
      Grammar::BLOCK_NAME_INDEX => '---'
    ],
    'syntax' => '--- ---',
    'variants' => space('---', ' ', '---')
  ],

  // COMMENT_SCENARIOS
  [
    'captures' => [
      Grammar::COMMENT_OPERATOR_INDEX => '>',
      Grammar::COMMENT_TEXT_INDEX => 'Comment Text'
    ],
    'syntax' => '> Comment Text',
    'variants' => space('>', 'Comment Text')
  ],

  // COPY_SCENARIOS
  [
    'captures' => [
      Grammar::NAME_UNESCAPED_INDEX => 'Name',
      Grammar::COPY_OPERATOR_INDEX => '<',
      Grammar::TEMPLATE_INDEX => 'Other Name'
    ],
    'syntax' => 'Name < Other Name',
    'variants' => space('Name', '<', 'Other Name')
  ],
  [
    'captures' => [
      Grammar::NAME_UNESCAPED_INDEX => 'Name',
      Grammar::COPY_OPERATOR_INDEX => '<',
      Grammar::TEMPLATE_INDEX => '<'
    ],
    'syntax' => 'Name < <',
    'variants' => space('Name', '<', ' ', '<')
  ],
  [
    'captures' => [
      Grammar::NAME_UNESCAPED_INDEX => 'Name',
      Grammar::COPY_OPERATOR_INDEX => '<<',
      Grammar::TEMPLATE_INDEX => '<'
    ],
    'syntax' => 'Name << <',
    'variants' => space('Name', '<<', '<')
  ],

  // FIELDSET_ENTRY_SCENARIOS
  [
    'captures' => [
      Grammar::NAME_UNESCAPED_INDEX => 'Name',
      Grammar::FIELDSET_ENTRY_OPERATOR_INDEX => '=',
      Grammar::FIELDSET_ENTRY_VALUE_INDEX => 'Value'
    ],
    'syntax' => 'Name = Value',
    'variants' => space('Name', '=', 'Value')
  ],
  [
    'captures' => [
      Grammar::NAME_UNESCAPED_INDEX => 'The Name',
      Grammar::FIELDSET_ENTRY_OPERATOR_INDEX => '=',
      Grammar::FIELDSET_ENTRY_VALUE_INDEX => 'The Value'
    ],
    'syntax' => 'The Name = The Value',
    'variants' => space('The Name', '=', 'The Value')
  ],
  [
    'captures' => [
      Grammar::NAME_UNESCAPED_INDEX => 'Name',
      Grammar::FIELDSET_ENTRY_OPERATOR_INDEX => '=',
      Grammar::FIELDSET_ENTRY_VALUE_INDEX => '='
    ],
    'syntax' => 'Name = =',
    'variants' => space('Name', '=', ' ', '=')
  ],
  [
    'captures' => [
      Grammar::NAME_UNESCAPED_INDEX => 'Name',
      Grammar::FIELDSET_ENTRY_OPERATOR_INDEX => '=',
      Grammar::FIELDSET_ENTRY_VALUE_INDEX => ':'
    ],
    'syntax' => 'Name = :',
    'variants' => space('Name', '=', ' ', ':')
  ],
  [
    'captures' => [
      Grammar::NAME_ESCAPE_BEGIN_OPERATOR_INDEX => '`',
      Grammar::NAME_ESCAPED_INDEX => '<=:',
      Grammar::NAME_ESCAPE_END_OPERATOR_INDEX => '`',
      Grammar::FIELDSET_ENTRY_OPERATOR_INDEX => '=',
      Grammar::FIELDSET_ENTRY_VALUE_INDEX => '`<=:`'
    ],
    'syntax' => '`<=:` = `<=:`',
    'variants' => space('`', '<=:', '`', '=', '`<=:`')
  ],
  [
    'captures' => [
      Grammar::NAME_ESCAPE_BEGIN_OPERATOR_INDEX => '```',
      Grammar::NAME_ESCAPED_INDEX => '<`=``:',
      Grammar::NAME_ESCAPE_END_OPERATOR_INDEX => '```',
      Grammar::FIELDSET_ENTRY_OPERATOR_INDEX => '=',
      Grammar::FIELDSET_ENTRY_VALUE_INDEX => '`<=:`'
    ],
    'syntax' => '```<`=``:``` = `<=:`',
    'variants' => space('```', '<`=``:', '```', '=', '`<=:`')
  ],

  // EMPTY_LINE_SCENARIOS
  [
    'captures' => [
      Grammar::EMPTY_LINE_INDEX => ''
    ],
    'syntax' => '',
    'variants' => space('')
  ],

  // FIELD_SCENARIOS
  [
    'captures' => [
      Grammar::NAME_UNESCAPED_INDEX => 'Name',
      Grammar::NAME_OPERATOR_INDEX => ':',
      Grammar::FIELD_VALUE_INDEX => 'Value'
    ],
    'syntax' => 'Name: Value',
    'variants' => space('Name', ':', 'Value')
  ],
  [
    'captures' => [
      Grammar::NAME_UNESCAPED_INDEX => 'The Name',
      Grammar::NAME_OPERATOR_INDEX => ':',
      Grammar::FIELD_VALUE_INDEX => 'The Value'
    ],
    'syntax' => 'The Name: The Value',
    'variants' => space('The Name', ':', 'The Value')
  ],
  [
    'captures' => [
      Grammar::NAME_UNESCAPED_INDEX => 'Name',
      Grammar::NAME_OPERATOR_INDEX => ':',
      Grammar::FIELD_VALUE_INDEX => ':'
    ],
    'syntax' => 'Name: :',
    'variants' => space('Name', ':', ' ', ':')
  ],
  [
    'captures' => [
      Grammar::NAME_ESCAPE_BEGIN_OPERATOR_INDEX => '`',
      Grammar::NAME_ESCAPED_INDEX => '<=:',
      Grammar::NAME_ESCAPE_END_OPERATOR_INDEX => '`',
      Grammar::NAME_OPERATOR_INDEX => ':',
      Grammar::FIELD_VALUE_INDEX => '`<=:`'
    ],
    'syntax' => '`<=:` : `<=:`',
    'variants' => space('`', '<=:', '`', ':', '`<=:`')
  ],
  [
    'captures' => [
      Grammar::NAME_ESCAPE_BEGIN_OPERATOR_INDEX => '```',
      Grammar::NAME_ESCAPED_INDEX => '<`=``:',
      Grammar::NAME_ESCAPE_END_OPERATOR_INDEX => '```',
      Grammar::NAME_OPERATOR_INDEX => ':',
      Grammar::FIELD_VALUE_INDEX => '`<=:`'
    ],
    'syntax' => '```<`=``:``` : `<=:`',
    'variants' => space('```', '<`=``:', '```', ':', '`<=:`')
  ],

  // INVALID_SCENARIOS
  [
    'syntax' => 'Invalid',
    'variants' => space('Invalid')
  ],
  [
    'syntax' => 'Invalid <',
    'variants' => space('Invalid', '<')
  ],
  [
    'syntax' => '< Invalid',
    'variants' => space('<', 'Invalid')
  ],
  [
    'syntax' => '<',
    'variants' => space('<')
  ],
  [
    'syntax' => '#',
    'variants' => space('#')
  ],
  [
    'syntax' => '--',
    'variants' => space('--')
  ],
  [
    'syntax' => ':',
    'variants' => space(':')
  ],
  [
    'syntax' => ': Invalid',
    'variants' => space(':', 'Invalid')
  ],
  [
    'syntax' => '=',
    'variants' => space('=')
  ],
  [
    'syntax' => '= Invalid',
    'variants' => space('=', 'Invalid')
  ],
  [
    'syntax' => '### `Invalid',
    'variants' => space('###', '`Invalid')
  ],
  [
    'syntax' => '---',
    'variants' => space('---')
  ],
  [
    'syntax' => "Invalid\nValid:",
    'variants' => space('Invalid', "\n", 'Valid', ':')
  ],
  [
    'syntax' => "Invalid\nValid:Valid",
    'variants' => space('Invalid', "\n", 'Valid', ':', 'Valid')
  ],

  // LIST_ITEM_SCENARIOS
  [
    'captures' => [
      Grammar::LIST_ITEM_OPERATOR_INDEX => '-'
    ],
    'syntax' => '-',
    'variants' => space('-')
  ],
  [
    'captures' => [
      Grammar::LIST_ITEM_OPERATOR_INDEX => '-',
      Grammar::LIST_ITEM_VALUE_INDEX => 'Item'
    ],
    'syntax' => '- Item',
    'variants' => space('-', 'Item')
  ],
  [
    'captures' => [
      Grammar::LIST_ITEM_OPERATOR_INDEX => '-',
      Grammar::LIST_ITEM_VALUE_INDEX => 'The Item'
    ],
    'syntax' => '- The Item',
    'variants' => space('-', 'The Item')
  ],
  [
    'captures' => [
      Grammar::LIST_ITEM_OPERATOR_INDEX => '-',
      Grammar::LIST_ITEM_VALUE_INDEX => '-'
    ],
    'syntax' => '- -',
    'variants' => space('-', ' ', '-')
  ],

  // NAME_SCENARIOS
  [
    'captures' => [
      Grammar::NAME_UNESCAPED_INDEX => 'Name',
      Grammar::NAME_OPERATOR_INDEX => ':'
    ],
    'syntax' => 'Name:',
    'variants' => space('Name', ':')
  ],
  [
    'captures' => [
      Grammar::NAME_UNESCAPED_INDEX => 'The Name',
      Grammar::NAME_OPERATOR_INDEX => ':'
    ],
    'syntax' => 'The Name:',
    'variants' => space('The Name', ':')
  ],
  [
    'captures' => [
      Grammar::NAME_ESCAPE_BEGIN_OPERATOR_INDEX => '`',
      Grammar::NAME_ESCAPED_INDEX => '<=:',
      Grammar::NAME_ESCAPE_END_OPERATOR_INDEX => '`',
      Grammar::NAME_OPERATOR_INDEX => ':'
    ],
    'syntax' => '`<=:`:',
    'variants' => space('`', '<=:', '`', ':')
  ],
  [
    'captures' => [
      Grammar::NAME_ESCAPE_BEGIN_OPERATOR_INDEX => '```',
      Grammar::NAME_ESCAPED_INDEX => '<`=``:',
      Grammar::NAME_ESCAPE_END_OPERATOR_INDEX => '```',
      Grammar::NAME_OPERATOR_INDEX => ':'
    ],
    'syntax' => '```<`=``:```:',
    'variants' => space('```', '<`=``:', '```', ':')
  ],

  // SECTION_SCENARIOS
  [
    'captures' => [
      Grammar::SECTION_HASHES_INDEX => '#',
      Grammar::SECTION_NAME_UNESCAPED_INDEX => 'Name'
    ],
    'syntax' => '# Name',
    'variants' => space('#', 'Name')
  ],
  [
    'captures' => [
      Grammar::SECTION_HASHES_INDEX => '##',
      Grammar::SECTION_NAME_UNESCAPED_INDEX => 'The Name'
    ],
    'syntax' => '## The Name',
    'variants' => space('##', 'The Name')
  ],
  [
    'captures' => [
      Grammar::SECTION_HASHES_INDEX => '#',
      Grammar::SECTION_NAME_UNESCAPED_INDEX => '#',
      Grammar::SECTION_COPY_OPERATOR_INDEX => '<',
      Grammar::SECTION_TEMPLATE_INDEX => 'Other Name'
    ],
    'syntax' => '# # < Other Name',
    'variants' => space('#', ' ', '#', '<', 'Other Name')
  ],
  [
    'captures' => [
      Grammar::SECTION_HASHES_INDEX => '###',
      Grammar::SECTION_NAME_UNESCAPED_INDEX => '##',
      Grammar::SECTION_COPY_OPERATOR_INDEX => '<',
      Grammar::SECTION_TEMPLATE_INDEX => '###'
    ],
    'syntax' => '### ## < ###',
    'variants' => space('###', ' ', '##', '<', '###')
  ],
  [
    'captures' => [
      Grammar::SECTION_HASHES_INDEX => '#',
      Grammar::SECTION_NAME_ESCAPE_BEGIN_OPERATOR_INDEX => '`',
      Grammar::SECTION_NAME_ESCAPED_INDEX => '<=:',
      Grammar::SECTION_NAME_ESCAPE_END_OPERATOR_INDEX => '`',
      Grammar::SECTION_COPY_OPERATOR_INDEX => '<',
      Grammar::SECTION_TEMPLATE_INDEX => '`<=:`'
    ],
    'syntax' => '# `<=:` < `<=:`',
    'variants' => space('#', '`<=:`', '<', '`<=:`')
  ],
  [
    'captures' => [
      Grammar::SECTION_HASHES_INDEX => '#',
      Grammar::SECTION_NAME_ESCAPE_BEGIN_OPERATOR_INDEX => '```',
      Grammar::SECTION_NAME_ESCAPED_INDEX => '<`=``:',
      Grammar::SECTION_NAME_ESCAPE_END_OPERATOR_INDEX => '```',
      Grammar::SECTION_COPY_OPERATOR_INDEX => '<',
      Grammar::SECTION_TEMPLATE_INDEX => '```<`=``:```'
    ],
    'syntax' => '# ```<`=``:``` < ```<`=``:```',
    'variants' => space('#', '```<`=``:```', '<', '```<`=``:```')
  ],
  [
    'captures' => [
      Grammar::SECTION_HASHES_INDEX => '#',
      Grammar::SECTION_NAME_UNESCAPED_INDEX => 'Name',
      Grammar::SECTION_COPY_OPERATOR_INDEX => '<<',
      Grammar::SECTION_TEMPLATE_INDEX => 'Other Name'
    ],
    'syntax' => '# Name << Other Name',
    'variants' => space('#', ' ', 'Name', '<<', 'Other Name')
  ]

];
