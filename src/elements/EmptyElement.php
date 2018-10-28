<?php

namespace Eno;
use Eno\Errors\Validation;

class EmptyElement {
  public $touched;

  function __construct($context, $instruction, $parent) {
    $this->context = $context;
    $this->instruction = $instruction;
    $this->name = $instruction->name;
    $this->parent = $parent;
    $this->touched = false;

    $instruction->element = $this;
  }

  public function __toString() {
    return "[EmptyElement name=\"{$this->name}\"]";
  }

  public function error($message = null) {
    if(!is_string($message) && is_callable($message)) {
      $message = $message($this->name, null);
    }

    return Validation::valueError($this->context, $message, $this->instruction);
  }

  public function raw() {
    return [ $this->name => null ];
  }

  public function touch() {
    $this->touched = true;
  }

  public function value() {
    $this->touched = true;
    return null;
  }
}
