<?php declare(strict_types=1);

require_once('src/analyzer.php');
require_once('src/tokenizer.php');

function inspectAnalysis($input) {
  $context = (object) [ 'input' => $input ];

  tokenize($context);
  analyze($context);

  return $context->instructions;
};
