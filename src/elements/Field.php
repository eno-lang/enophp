<?php

namespace Eno;

class Field {
  public $touched;
  private $value = null;

  function __construct(&$context, &$instruction, &$parent, $from_empty = false) {
    $this->context = $context;
    $this->instruction = $instruction;
    $this->name = $instruction['name'];
    $this->parent = $parent;
    $this->value = $instruction['value'];
    $this->touched = false;

    if($from_empty)
      return;

    $instruction['element'] = $this;

    // ... TODO
  }

  public function __toString() {
    $value = $this->value;

    if($value === null) {
      $value = 'null';
    } else {
      $value = str_replace("\n", '\n', $value);
      if(strlen($value) > 14) {
        $value = substr($value, 0, 11) . '...';
      }
      $value = "\"{$value}\"";
    }

    if($this->name === null) {
      return "[Field value={$value}]";
    } else {
      return "[Field name=\"{$this->name}\" value={$value}]";
    }
  }

  function isEmpty() {
    return $this->value === null;
  }

  function raw() {
    if($this->name === null) {
      return $this->value;
    } else {
      return [ $this->name => $this->value ];
    }
  }

  function touch() {
    $this->touched = true;
  }
}

?>
