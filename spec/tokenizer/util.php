<?php

require_once('src/tokenizer.php');

function inspectTokenization(&$input) {
  $context = [ 'input' => $input ];

  tokenize($context);

  return $context['instructions'];
};
