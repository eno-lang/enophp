<?php

$input = <<<DOC
> Comment

language:
language: eno

entry = value
- list item
| continue
\ continue
DOC;

require_once('tokenizer.php');

$context = [
  'indexing' => 1,
  'input' => $input,
  'locale' => 'en'
];

tokenize($context);

print_r($context);

?>
