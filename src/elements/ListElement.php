<?php

namespace Eno;
use Eno\ValidationError;
use \stdClass;

class ListElement {
  public $touched;

  function __construct(stdClass $context, stdClass $instruction, Section $parent, bool $from_empty = false) {
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

  public function __toString() : string {
    $items_count = count($this->items);
    return "[List name=\"{$this->name}\" items={$items_count}]";
  }

  public function elements() : array {
    $this->touched = true;
    return $this->items;
  }

  public function items(...$optional) : array {
    $options = [
      'elements' => false,
      'enforce_values' => true,
      'with_elements' => false
    ];

    $loader = null;

    foreach($optional as $argument) {
      if(is_callable($argument)) {
        $loader = $argument;
      } else {
        $options = array_merge($options, $argument);
      }
    }

    $this->touched = true;

    if($options['elements'])
      return $this->items;

    if($options['with_elements']) {
      return array_map(
        function($item) {
          return [
            'element' => $item,
            'value' => $item->value($loader, [ 'enforce_value' => $options['enforce_values'] ])
          ];
        },
        $this->items
      );
    }

    return array_map(
      function($item) { return $item->value($loader, [ 'enforce_value' => $options['enforce_values'] ]); },
      $this->items
    );
  }

  public function length() : int {
    return count($this->items);
  }

  public function raw() : array {
    return [
      $this->name => array_map(
        function($item) { return $item->value(); },
        $this->items
      )
    ];
  }

  public function touch(array $options = []) : void {
    $default_options = [ 'items' => false ];

    $options = array_merge($default_options, $options);

    $this->touched = true;

    if($options['items']) {
      foreach($this->items as $item) {
        $item->touch();
      }
    }
  }
}
