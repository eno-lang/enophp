<?php

namespace Eno;
use Eno\Errors\Validation;

class Section {
  public $touched;

  function __construct($context, $instruction, $parent) {
    $this->context = $context;
    $this->depth = $instruction->depth;
    $this->instruction = $instruction;
    $this->name = $instruction->name;
    $this->parent = $parent;

    $this->elements = [];
    $this->elements_associative = [];
    $this->enforce_all_elements = false;
    $this->touched = false;

    $instruction->element = $this;

    $append = function($element) {
      $this->elements[] = $element;

      if(array_key_exists($element->name, $this->elements_associative)) {
        $this->elements_associative[$element->name][] = $element;
      } else {
        $this->elements_associative[$element->name] = [$element];
      }
    };

    foreach($instruction->subinstructions as $subinstruction) {
      if($subinstruction->type == 'NOOP') {
        $subinstruction->element = $this;
      } else if($subinstruction->type == 'NAME') {
        $append(new EmptyElement($context, $subinstruction, $this));
      } else if($subinstruction->type == 'FIELD') {
        $append(new Field($context, $subinstruction, $this));
      } else if($subinstruction->type == 'LIST') {
        $append(new ListElement($context, $subinstruction, $this));
      } else if($subinstruction->type == 'BLOCK') {
        $append(new Field($context, $subinstruction, $this));
      } else if($subinstruction->type == 'FIELDSET') {
        $append(new Fieldset($context, $subinstruction, $this));
      } else if($subinstruction->type == 'SECTION') {
        $append(new Section($context, $subinstruction, $this));
      }
    }
  }

  // TODO: 'options.required' was not kept here (only enforce_element)
  //       Consider how to deal with this generally in enophp
  public function element($name, $enforce_element = true) {
    $this->touched = true;

    if(!array_key_exists($name, $this->elements_associative)) {
      if($enforce_element)
        throw Validation::missingElement($this->context, $name, $this->instruction);

      return null;
    }

    $elements = $this->elements_associative[$name];

    if(count($elements) > 1) {
      throw Validation::expectedElementGotElements(
        $this->context,
        $name,
        array_map(function($element) { return $element->instruction; }, $elements)
      );
    }

    $element = $elements[0];

    $element->touch();

    return $element;
  }

  public function raw() {
    $elements = array_map(
      function($element) { return $element->raw(); },
      $this->elements
    );

    if($this->name == '<>#:=|\\_ENO_DOCUMENT')
      return $elements;

    return [ $this->name => $elements ];
  }
}
