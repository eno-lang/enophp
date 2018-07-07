<?php

// $input = <<<DOC
// > Comment
//
// language:
// language: eno
//
// entry = value
// - list item
// | continue
// \ continue
// DOC;

$input = file_get_contents('./sample.eno');

require_once('tokenizer.php');

$context = [
  'indexing' => 1,
  'input' => $input,
  'locale' => 'en'
];

tokenize($context);

print_r($context);

?>
