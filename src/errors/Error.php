<?php

namespace Eno;
use Exception;

class Error extends Exception {
  public $cursor;
  public $message;
  public $selection;
  public $snippet;
  public $text;

  public function __construct($text, $snippet, $selection) {
    $this->cursor = $selection[0];
    $this->message = $text . "\n\n" . $snippet;
    $this->selection = $selection;
    $this->snippet = $snippet;
    $this->text = $text;

    parent::__construct($this->message, 0);
  }

  // TODO: Temporarily kept for reference (from http://www.php.net/manual/en/language.exceptions.php)
  // public function __toString() {
  //   return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n"
  //                           . "{$this->getTraceAsString()}";
  // }
}

class ParseError extends Error { }
class ValidationError extends Error { }
