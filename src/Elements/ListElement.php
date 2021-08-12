<?php declare(strict_types=1);

namespace Eno\Elements;
use Eno\Elements\Field;
use Eno\Elements\Section;
use Eno\Errors\ValidationError;
use \BadMethodCallException;
use \Closure;
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
      if($subinstruction->type === 'LIST_ITEM') {
        $subinstruction->element = new Field($context, $subinstruction, $this);
        $this->items[] = $subinstruction->element;
      } else {
        $subinstruction->element = $this;
      }
    }
  }

  public function __call($function_name, $arguments) {
    $function_name = substr($function_name, 0, -5);

    if(method_exists('Eno\Loaders', $function_name)) {
      return $this->items(Closure::fromCallable(['Eno\\Loaders', $function_name]), ...$arguments);
    } else {
      throw new BadMethodCallException("Call to undefined method Eno\\Elements\\Section::{$function_name}()");
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
      if($argument === null)
        continue;

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
        function($item) use($loader, $options) {
          return [
            'element' => $item,
            'value' => $item->value($loader, [ 'enforce_value' => $options['enforce_values'] ])
          ];
        },
        $this->items
      );
    }

    return array_map(
      function($item) use($loader, $options) {
        return $item->value($loader, [ 'enforce_value' => $options['enforce_values'] ]);
      },
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

  public function stringItems(...$arguments) {
    return $this->items(...$arguments);
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
