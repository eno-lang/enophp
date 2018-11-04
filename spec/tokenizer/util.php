<?php declare(strict_types=1);

require_once('src/tokenizer.php');

function inspectTokenization($input) {
  $context = (object) [ 'input' => $input ];

  tokenize($context);

  return $context->instructions;
};
