<?php

namespace Eno;
use Eno\Errors\Validation;
use \stdClass;

class Section {
  public $touched;

  function __construct(stdClass $context, stdClass $instruction, Section $parent = null) {
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

  public function __toString() : string {
    $elements_count = count($this->elements);

    if($this->name == '<>#:=|\\_ENO_DOCUMENT')
      return "[Section document elements={$elements_count}]";

    return "[Section name=\"{$this->name}\" elements={$elements_count}]";
  }

  public function assertAllTouched(array $options = []) : void {
    $default_options = [
      'message' => null,
      'except' => null,
      'only' => null
    ];

    $options = array_merge($default_options, $options);

    foreach($this->elements_associative as $name => $elements) {
      if($options['except'] && in_array($name, $options['except'])) continue;
      if($options['only'] && !in_array($name, $options['only'])) continue;

      foreach($elements as $element) {
        if(!$element->touched) {
          if(!is_string($message) && is_callable($message)) {
            $value = $element instanceof Fieldset ||
                     $element instanceof Section ?
                          null : $element->value();

            $message = $message($element->name, $value);
          }

          throw Validation::excessName($this->context, $message, $element->instruction);
        }

        if($element instanceof Fieldset || $element instanceof Section) {
          $element->assertAllTouched($message);
        }
      }
    }
  }

  public function element(string $name, array $options = []) : object {
    $default_options = [
      'enforce_element' => true,
      'required' => null
    ];

    $options = array_merge($default_options, $options);

    if($options['required'] !== null) {
      $options['enforce_element'] = $options['required'];
    }

    $this->touched = true;

    if(!array_key_exists($name, $this->elements_associative)) {
      if($options['enforce_element'])
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

  public function elements() : array {
    $this->touched = true;

    foreach($this->elements as $element) {
      $element->touch();
    }

    return $this->elements;
  }

  public function enforceAllElements(bool $enforce = true) : void {
    $this->enforce_all_elements = $enforce;

    foreach($this->elements as $element) {
      if($element instanceof Fieldset || $element instanceof Section) {
        $element->enforceAllElements($enforce);
      }
    }
  }

  public function field(string $name, ...$optional) {
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
        $loader =  $argument;
      } else {
        $options = array_merge($options, $argument);
      }
    }

    if($options['required'] !== null) {
      $options['enforce_value'] = $options['required'];
    }

    $options['enforce_element'] = $options['enforce_element'] || $options['enforce_value'];

    $this->touched = true;

    if(!array_key_exists($name, $this->elements_associative)) {
      if($options['enforce_element'])
        throw Validation::missingField($this->context, $name, $this->instruction);

      if($options['with_element'])
        return [ 'element' => null, 'value' => null ];

      return null;
    }

    $elements = $this->elements_associative[$name];

    foreach($elements as $element) {
      if($element instanceof Field || $element instanceof EmptyElement)
        continue;

      if($element instanceof Fieldset)
        throw Validation::expectedFieldGotFieldset($this->context, $element->instruction);

      if($element instanceof ListElement)
        throw Validation::expectedFieldGotList($this->context, $element->instruction);

      if($element instanceof Section)
        throw Validation::expectedFieldGotSection($this->context, $element->instruction);
    }

    if(count($elements) > 1) {
      throw Validation::expectedFieldGotFields(
        $this->context,
        $name,
        array_map(function($element) { return $element->instruction; }, $elements)
      );
    }

    $element = $elements[0];

    $element->touch();

    if($element instanceof EmptyElement) {
      $element = new Field($this->context, $element->instruction, $this, true);
    }

    if($options['element'])
      return element;

    if($options['with_element']) {
      return [
        'element' => $element,
        'value' => $element->value($loader, [ 'enforce_value' => $options['enforce_value'] ])
      ];
    }

    return $element->value($loader, [ 'enforce_value' => $options['enforce_value'] ]);
  }

  public function fields(string $name) : array {
    $this->touched = true;

    if(!array_key_exists($name, $this->elements_associative))
      return [];

    $elements = $this->elements_associative[$name];

    return array_map(
      function($element) {
        $element->touch();

        if($element instanceof Field)
          return $element;

        if($element instanceof EmptyElement)
          return new Field($this->context, $element->instruction, $this, true);

        if($element instanceof Fieldset)
          throw Validation::expectedFieldsGotFieldset($this->context, $element->instruction);

        if($element instanceof ListElement)
          throw Validation::expectedFieldsGotList($this->context, $element->instruction);

        if($element instanceof Section)
          throw Validation::expectedFieldsGotSection($this->context, $element->instruction);
      },
      $elements
    );
  }

  public function fieldset(string $name, array $options = []) : object {
    $default_options = [
      'enforce_element' => true,
      'required' => null
    ];

    $options = array_merge($default_options, $options);

    if($options['required'] !== null) {
      $options['enforce_element'] = $options['required'];
    }

    $this->touched = true;

    if(!array_key_exists($name, $this->elements_associative)) {
      if($options['enforce_element'])
        throw Validation::missingFieldset($this->context, $name, $this->instruction);

      return null;
    }

    $elements = $this->elements_associative[$name];

    foreach($elements as $element) {
      if($element instanceof Fieldset || $element instanceof EmptyElement)
        continue;

      if($element instanceof ListElement)
        throw Validation::expectedFieldsetGotList($this->context, $element->instruction);

      if($element instanceof Section)
        throw Validation::expectedFieldsetGotSection($this->context, $element->instruction);

      if($element instanceof Field)
        throw Validation::expectedFieldsetGotField($this->context, $element->instruction);
    }

    if(count($elements) > 1) {
      throw Validation::expectedFieldsetGotFieldsets(
        $this->context,
        $name,
        array_map(function($element) { return $element->instruction; }, $elements)
      );
    }

    $element = $elements[0];

    $element->touch();

    if($element instanceof EmptyElement)
      return new Fieldset($this->context, $element->instruction, $this, true);

    return $element;
  }

  public function fieldsets(string $name) : array {
    $this->touched = true;

    if(!array_key_exists($name, $this->elements_associative))
      return [];

    $elements = $this->elements_associative[$name];

    return array_map(
      function($element) {
        $element->touch();

        if($element instanceof Fieldset)
          return $element;

        if($element instanceof EmptyElement)
          return new Fieldset($this->context, $element->instruction, $this, true);

        if($element instanceof ListElement)
          throw Validation::expectedFieldsetsGotList($this->context, $element->instruction);

        if($element instanceof Section)
          throw Validation::expectedFieldsetsGotSection($this->context, $element->instruction);

        if($element instanceof Field)
          throw Validation::expectedFieldsetsGotField($this->context, $element->instruction);
      },
      $elements
    );
  }

  public function list(string $name, ...$optional) {
    $options = [
      'element' => false,
      'elements' => false,
      'enforce_element' => $this->enforce_all_elements,
      'enforce_values' => true,
      'exact_count' => null,
      'max_count' => null,
      'min_count' => null,
      'required' => null,
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

    if($options['required'] !== null) {
      $options['enforce_element'] = $options['required'];
    }

    $options['enforce_element'] = $options['enforce_element'] ||
                                  $options['exact_count'] !== null && $options['exact_count'] > 0 ||
                                  $options['min_count'] !== null && $options['min_count'] > 0;

    $this->touched = true;

    if(!array_key_exists($name, $this->elements_associative)) {
      if($options['enforce_element'])
        throw Validation::missingList($this->context, $name, $this->instruction);

      return [];
    }

    $elements = $this->elements_associative[$name];

    foreach($elements as $element) {
      if($element instanceof ListElement || $element instanceof EmptyElement)
        continue;

      if($element instanceof Field)
        throw Validation::expectedListGotField($this->context, $element->instruction);

      if($element instanceof Fieldset)
        throw Validation::expectedListGotFieldset($this->context, $element->instruction);

      if($element instanceof Section)
        throw Validation::expectedListGotSection($this->context, $element->instruction);
    }

    if(count($elements) > 1) {
      throw Validation::expectedListGotLists(
        $this->context,
        $name,
        array_map(function($element) { return $element->instruction; }, $elements)
      );
    }

    $element = $elements[0];

    $element->touch();

    $count = $element instanceof EmptyElement ? 0 : $element->length();

    if($options['exact_count'] !== null && $count !== $options['exact_count'])
      throw Validation::exactCountNotMet($this->context, $element->instruction, $options['exact_count']);

    if($options['min_count'] !== null && $count < $options['min_count'])
      throw Validation::minCountNotMet($this->context, $element->instruction, $options['min_count']);

    if($options['max_count'] !== null && $count > $options['max_count'])
      throw Validation::maxCountNotMet($this->context, $element->instruction, $options['max_count']);

    if($element instanceof EmptyElement)
      return [];

    if($options['element'])
      return $element;

    return $element->items($loader, [
      'elements' => $options['elements'],
      'enforce_values' => $options['enforce_values'],
      'with_elements' => $options['with_elements']
    ]);
  }

  public function lists(string $name) : array {
    if(!array_key_exists($name, $this->elements_associative))
      return [];

    $elements = $this->elements_associative[$name];

    return array_map(
      function($element) {
        $element->touch();

        if($element instanceof ListElement) {
          return $element;
        }

        if($element instanceof EmptyElement) {
          return new ListElement($this->context, $element->instruction, $this, true);
        }

        if($element instanceof Fieldset) {
          throw errors.expectedListsGotFieldset($this->context, $element->instruction);
        }

        if($element instanceof Section) {
          throw errors.expectedListsGotSection($this->context, $element->instruction);
        }

        if($element instanceof Field) {
          throw errors.expectedListsGotField($this->context, $element->instruction);
        }
      },
      $elements
    );
  }

  public function lookup(...$position) : array {
    $line = null;
    $column = null;

    if(count($position) == 2) {
      $line = $position[0];
      $column = $position[1];
    } else {
      $index_argument = $position[0];
      $index = 0;
      $line = 0;
      $column = 0;
      while($index != $index_argument) {
        if($index >= strlen($this->context->input))
          return null;

        if($this->context->input[$index] == "\n") {
          $line++;
          $column = 0;
        } else {
          $column++;
        }

        $index++;
      }
    }

    $instruction = null;
    foreach($this->context->instructions as $find_instruction) {
      if($find_instruction->line == $line) {
        $instruction = $find_instruction;
        break;
      }
    }

    if(!$instruction)
      return null;

    $result = [
      'element' => $instruction->element,
      'zone' => 'element'
    ];

    if($instruction->ranges) {
      $rightmost_match = 0;

      foreach($instruction->ranges as $type => $range) {
        if($column >= $range[0] && $column <= $range[1] && $range[0] >= $rightmost_match) {
          $result->zone = $type;
          $rightmost_match = $column;
        }
      }
    }

    return $result;
  }

  public function raw() : array {
    $elements = array_map(
      function($element) { return $element->raw(); },
      $this->elements
    );

    if($this->name == '<>#:=|\\_ENO_DOCUMENT')
      return $elements;

    return [ $this->name => $elements ];
  }

  public function section(string $name, array $options = []) : object {
    $default_options = [
      'enforce_element' => true,
      'required' => null
    ];

    $options = array_merge($default_options, $options);

    if($options['required'] !== null) {
      $options['enforce_element'] = $options['required'];
    }

    $this->touched = true;

    if(!array_key_exists($name, $this->elements_associative)) {
      if($options['enforce_element'])
        throw Validation::missingSection($this->context, $name, $this->instruction);

      return null;
    }

    $elements = $this->elements_associative[$name];

    foreach($elements as $element) {
      if($element instanceof Section)
        continue;

      if($element instanceof Fieldset)
        throw Validation::expectedSectionGotFieldset($this->context, $element->instruction);

      if($element instanceof EmptyElement)
        throw Validation::expectedSectionGotEmpty($this->context, $element->instruction);

      if($element instanceof ListElement)
        throw Validation::expectedSectionGotList($this->context, $element->instruction);

      if($element instanceof Field)
        throw Validation::expectedSectionGotField($this->context, $element->instruction);
    }

    if(count($elements) > 1) {
      throw Validation::expectedSectionGotSections(
        $this->context,
        $name,
        array_map(function($element) { return $element->instruction; }, $elements)
      );
    }

    $elements[0]->touch();

    return $elements[0];
  }

  public function sections(string $name) : array {
    $this->touched = true;

    if(!array_key_exists($name, $this->elements_associative))
      return [];

    $elements = $this->elements_associative[$name];

    foreach($elements as $element) {
      $element->touch();

      if($element instanceof Section)
        continue;

      if($element instanceof Fieldset)
        throw Validation::expectedSectionsGotFieldset($this->context, $element->instruction);

      if($element instanceof EmptyElement)
        throw Validation::expectedSectionsGotEmpty($this->context, $element->instruction);

      if($element instanceof ListElement)
        throw Validation::expectedSectionsGotList($this->context, $element->instruction);

      if($element instanceof Field)
        throw Validation::expectedSectionsGotField($this->context, $element->instruction);
    }

    return $elements;
  }

  public function touch() : void {
    $this->touched = true;
  }
}
