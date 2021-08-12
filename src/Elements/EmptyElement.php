<?php declare(strict_types=1);

namespace Eno\Elements;
use Eno\Elements\Section;
use Eno\Errors\Validation;
use Eno\Errors\ValidationError;
use \stdClass;

class EmptyElement {
  public $touched;

  function __construct(stdClass $context, stdClass $instruction, Section $parent) {
    $this->context = $context;
    $this->instruction = $instruction;
    $this->name = $instruction->name;
    $this->parent = $parent;
    $this->touched = false;

    $instruction->element = $this;
  }

  public function __toString() : string {
    return "[EmptyElement name=\"{$this->name}\"]";
  }

  public function error($message = null) : ValidationError {
    if(!is_string($message) && is_callable($message)) {
      $message = $message($this->name, null);
    }

    return Validation::valueError($this->context, $message, $this->instruction);
  }

  public function raw() : array {
    return [ $this->name => null ];
  }

  public function touch() : void {
    $this->touched = true;
  }

  public function value() {
    $this->touched = true;
    return null;
  }
}
