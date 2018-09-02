<?php

namespace Eno;

class EmptyElement {
  public $touched;
  private $value = null;

  function __construct(&$context, &$instruction, &$parent) {
    $this->context = $context;
    $this->instruction = $instruction;
    $this->name = $instruction['name'];
    $this->parent = $parent;
    $this->touched = false;

    $instruction['element'] = $this;
  }

  public function __toString() {
    return "[EmptyElement name=\"{$this->name}\"]";
  }

  function error($message) {
    // TODO
  }

  function raw() {
    return [ $this->name => null ];
  }

  function touch() {
    $this->touched = true;
  }

  function value() {
    $this->touched = true;
    return null;
  }
}
