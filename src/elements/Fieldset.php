<?php

namespace Eno;
use \stdClass;

class Fieldset {
  public $touched;

  function __construct(stdClass $context, stdClass $instruction, Section $parent, bool $from_empty = false) {
    $this->context = $context;
    $this->instruction = $instruction;
    $this->name = $instruction->name;
    $this->parent = $parent;

    $this->entries = [];
    $this->entries_associative = [];

    if($from_empty) {
      $this->enforce_all_elements = $parent->enforce_all_elements;
      $this->touched = true;
    } else {
      $instruction->element = $this;

      $this->touched = false;
      $this->enforce_all_elements = false;

      foreach($instruction->subinstructions as $subinstruction) {
        if($subinstruction->type == 'FIELDSET_ENTRY') {
          $subinstruction->element = new Field($context, $subinstruction, $this);
          $this->entries[] = $subinstruction->element;
          $this->entries_associative[$subinstruction->name] = $subinstruction->element;
        } else {
          $subinstruction->element = $this;
        }
      }
    }
  }

  public function raw() {
    return [
      $this->name => array_map(
        function($entry) { return [ $entry->name => $entry->value() ]; },
        $this->entries
      )
    ];
  }
}
