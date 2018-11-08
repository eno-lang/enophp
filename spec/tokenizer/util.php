<?php declare(strict_types=1);

require_once(__DIR__ . '/../../src/tokenizer.php');

function inspectTokenization($input) {
  $context = (object) [ 'input' => $input ];

  tokenize($context);

  return $context->instructions;
};
