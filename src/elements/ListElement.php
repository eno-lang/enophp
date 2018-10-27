<?php

namespace Eno;

class ListElement {
  public $touched;

  function __construct($context, $instruction, $parent, $from_empty = false) {
    $this->context = $context;
    $this->instruction = $instruction;
    $this->name = $instruction->name;
    $this->parent = $parent;
    $this->touched = false;
    $this->items = [];

    if($from_empty)
      return;

    $instruction->element = $this;

    foreach($instruction->subinstructions as $subinstruction) {
      if($subinstruction->type == 'LIST_ITEM') {
        $subinstruction->element = new Field($context, $subinstruction, $this);
        $this->items[] = $subinstruction->element;
      } else {
        $subinstruction->element = $this;
      }
    }
  }

  public function raw() {
    return [
      $this->name => array_map(
        function($item) { return $item->value(); },
        $this->items
      )
    ];
  }
}
