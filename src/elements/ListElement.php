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

  public function __toString() {
    return "[List name=\"{$this->name}\" items={count($this->items)}]";
  }

  public function elements() {
    $this->touched = true;
    return $this->items;
  }

  // TODO: Think of something for all the method signatures, let's use this opportunity to think about methods and signatures for eno in general again
  public function items($loader = null, $enforce_values = true, $with_elements = false, $return_elements = false) {
    $this->touched = true;

    if($return_elements)
      return $this->items;

    if($with_elements) {
      return array_map(
        function($item) {
          return [
            'element' => $item,
            'value' => $item->value($loader, $enforce_values)
          ];
        },
        $this->items
      );
    }

    return array_map(
      function($item) { return $item->value($loader, $enforce_values); },
      $this->items
    );
  }

  public function length() {
    return count($this->items);
  }

  public function raw() {
    return [
      $this->name => array_map(
        function($item) { return $item->value(); },
        $this->items
      )
    ];
  }

  public function touch($items = false) {
    $this->touched = true;

    if($items) {
      foreach($this->items as $item) {
        $item->touch();
      }
    }
  }
}
