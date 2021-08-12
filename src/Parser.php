<?php declare(strict_types=1);

namespace Eno;
use \OutOfRangeException;
use Eno\Elements\Section;
use Eno\Reporters\Reporter;

require_once(__DIR__ . '/analyzer.php'); // TODO: Class (?)
require_once(__DIR__ . '/tokenizer.php'); // TODO: Class (?)
require_once(__DIR__ . '/resolver.php'); // TODO: Class (?)

class Parser {
  public static function parse(string $input, array $options = []) : Section {
    $default_options = [
        'locale' => 'en',
        'reporter' => new Reporters\Text,
        'source_label' => null,
        'zero_indexing' => false
    ];

    $options = array_merge($default_options, $options);

    require(__DIR__ . '/messages.php'); // TODO: Refactor to a class or something.

    if(!array_key_exists($options['locale'], $MESSAGES)) {
      throw new OutOfRangeException(
        "The requested locale '{$options['locale']}' is not available. Translation contributions are " .
        "greatly appreciated, visit https://github.com/eno-lang/eno-locales if you wish to contribute."
      );
    }

    $context = (object) [
      'locale' => $options['locale'],
      'indexing' => $options['zero_indexing'] ? 0 : 1,
      'input' => $input,
      'messages' => $MESSAGES[$options['locale']],
      'reporter' => $options['reporter'],
      'source_label' => $options['source_label']
    ];

    tokenize($context);
    analyze($context);
    resolveFromContext($context);

    $context->document = new Section($context, $context->document_instruction);

    return $context->document;
  }
}
