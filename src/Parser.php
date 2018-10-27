<?php

namespace Eno;
use Eno\Section;

require_once('analyzer.php'); // TODO: Class (?)
require_once('tokenizer.php'); // TODO: Class (?)
require_once('resolver.php'); // TODO: Class (?)

class Parser {
  public static function parse($input, $locale = 'en', $reporter = null) {
    $context = (object) [];

    $context->locale = $locale;
    $context->indexing = 1;
    $context->input = $input;
    $context->source_label = null;

    if($reporter == null) {
      $context->reporter = new Reporters\Text;
    } else {
      $context->reporter = $reporter;
    }

    require('src/messages.php');  // TODO: Refactor to a class or something.

    $context->messages = $MESSAGES[$context->locale];

    if(!array_key_exists($locale, $MESSAGES)) {
      throw new Error(
        "The requested locale '{$locale}' is not available. Translation contributions are " .
        "greatly appreciated, visit https://github.com/eno-lang/eno-locales if you wish to contribute."
      );
    }

    tokenize($context);
    analyze($context);
    resolve($context);

    $context->document = new Section($context, $context->document_instruction, null);

    return $context->document;
  }
}
