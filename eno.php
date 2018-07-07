<?php
  require_once('tokenizer.php');

  $context = [
    'indexing' => 1,
    'input' => 'language: eno',
    'locale' => 'en'
  ];

  tokenize($context);

  print_r($context);
?>
