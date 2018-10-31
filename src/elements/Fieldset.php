<?php

namespace Eno;
use Eno\Errors\Validation;
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

  public function __toString() : string {
    $entries_count = count($this->entries);
    return "[Fieldset name=\"{$this->name}\" entries={$entries_count}]";
  }

  public function assertAllTouched(...$optional) : void {
    $options = [
      'except' => null,
      'only' => null
    ];

    $message = null;

    foreach($optional as $argument) {
      if(is_string($argument) || is_callable($argument)) {
        $message = $argument;
      } else {
        $options = array_merge($options, $argument);
      }
    }

    foreach($this->entries as $entry) {
      if($options['except'] && in_array($entry->name, $options['except'])) continue;
      if($options['only'] && !in_array($entry->name, $options['only'])) continue;

      if(!$entry->touched) {
        if(!is_string($message) && is_callable($message)) {
          $message = $message($entry->name, $entry->value());
        }

        throw Validation::excessName($this->context, $message, $entry->instruction);
      }
    }
  }

  public function element(string $name, array $options = []) : ?Field {
    $default_options = [
      'enforce_element' => true,
      'required' => null
    ];

    $options = array_merge($default_options, $options);

    if($options['required'] !== null) {
      $options['enforce_element'] = $options['required'];
    }

    $this->touched = true;

    if(!array_key_exists($name, $this->entries_associative)) {
      if($options['enforce_element']) {
        throw Validation::missingFieldsetEntry($this->context, $name, $this->instruction);
      }

      return null;
    }

    return $this->entries_associative[$name];
  }

  public function elements() : array {
    $this->touched = true;
    return $this->entries;
  }

  public function enforceAllElements(bool $enforce = true) : void {
    $this->enforce_all_elements = $enforce;
  }

  public function entries() : array {
    return $this->elements();
  }

  public function entry(string $name, ...$optional) {
    $options = [
      'element' => false,
      'enforce_element' => $this->enforce_all_elements,
      'enforce_value' => false,
      'required' => null,
      'with_element' => false
    ];

    $loader = null;

    foreach($optional as $argument) {
      if(is_callable($argument)) {
        $loader = $argument;
      } else {
        $options = array_merge($options, $argument);
      }
    }

    if($options['required'] !== null) {
      $options['enforce_value'] = $options['required'];
    }

    $options['enforce_element'] = $options['enforce_element'] || $options['enforce_value'];

    $this->touched = true;

    if(!array_key_exists($name, $this->entries_associative)) {
      if($options['enforce_element'])
        throw Validation::missingFieldsetEntry($this->context, $name, $this->instruction);

      if($options['with_element'])
        return [ 'element' => null, 'value' => null ];

      return null;
    }

    $element = $this->entries_associative[$name];

    $element->touch();

    if($options['element'])
      return $element;

    if($options['with_element']) {
      return [
        'element' => $element,
        'value' => $element->value($loader, [ 'enforce_value' => $options['enforce_value'] ])
      ];
    }

    return $element->value($loader, [ 'enforce_value' => $options['enforce_value'] ]);
  }

  public function raw() : array {
    return [
      $this->name => array_map(
        function($entry) { return [ $entry->name => $entry->value() ]; },
        $this->entries
      )
    ];
  }

  public function touch(array $options = []) : void {
    $default_options = [ 'entries' => false ];

    $options = array_merge($default_options, $options);

    $this->touched = true;

    if($options['entries']) {
      foreach($this->entries as $entry) {
        $entry->touch();
      }
    }
  }
}
