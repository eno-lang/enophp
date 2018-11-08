<?php declare(strict_types=1);

require_once(__DIR__ . '/../../src/analyzer.php');
require_once(__DIR__ . '/../../src/tokenizer.php');

function inspectAnalysis($input) {
  $context = (object) [ 'input' => $input ];

  tokenize($context);
  analyze($context);

  return $context->instructions;
};
