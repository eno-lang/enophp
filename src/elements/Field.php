<?php declare(strict_types=1);

namespace Eno;
use Eno\Errors\Validation;
use Eno\ValidationError;
use \BadMethodCallException;
use \Closure;
use \Exception;
use \stdClass;

class Field {
  public $touched;

  function __construct(stdClass $context, stdClass $instruction, object $parent, bool $from_empty = false) {
    $this->context = $context;
    $this->instruction = $instruction;
    $this->name = $instruction->name;
    $this->parent = $parent;
    $this->value = property_exists($instruction, 'value') ? $instruction->value : null;
    $this->touched = false;

    if($from_empty)
      return;

    $instruction->element = $this;

    if($instruction->type == 'BLOCK' && array_key_exists('content_range', $instruction)) {
      $this->value = substr($context->input, $instruction->content_range[0], $instruction->content_range[1] + 1);

      foreach($instruction->subinstructions as $subinstruction) {
        $subinstruction->element = $this;
      }
    } else if($instruction->subinstructions) {
      $unresolved_newlines = 0;

      foreach($instruction->subinstructions as $subinstruction) {
        $subinstruction->element = $this;

        if($subinstruction->type == 'CONTINUATION') {
          if($this->value === null) {
            $this->value = $subinstruction->value;
          } else {
            if($subinstruction->value === null) {
              if($subinstruction->separator === "\n") {
                $unresolved_newlines++;
              }
            } else {
              if($unresolved_newlines > 0) {
                $this->value .= str_repeat("\n", $unresolved_newlines);
                $unresolved_newlines = 0;
              }

              $this->value .= $subinstruction->separator . $subinstruction->value;
            }
          }
          continue;
        } else if($subinstruction->type == 'BLOCK' && array_key_exists('content_range', $subinstruction)) {
          // blocks can only appear as a subinstruction when they are copied,
          // and as a copy they always appear as the first subinstruction,
          // that is why write to value straight without any checks->
          $this->value = substr(
            $context->input,
            $subinstruction['content_range'][0],
            $subinstruction['content_range'][1] + 1
          );
        }
      }
    }
  }

  public function __call($function_name, $arguments) {
    if(method_exists('Eno\Loaders', $function_name)) {
      return $this->value(Closure::fromCallable(['Eno\\Loaders', $function_name]), ...$arguments);
    } else {
      throw new BadMethodCallException("Call to undefined method Eno\\Field::{$function_name}()");
    }
  }

  public function __toString() : string {
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

  public function error($message = null) : ValidationError {
    if(!is_string($message) && is_callable($message)) {
      $message = $message($this->name, $this->value);
    }

    return Validation::valueError($this->context, $message, $this->instruction);
  }

  public function isEmpty() : bool {
    return $this->value === null;
  }

  public function raw() {
    if($this->name === null) {
      return $this->value;
    } else {
      return [ $this->name => $this->value ];
    }
  }

  public function string(...$arguments) {
    return $this->value(...$arguments);
  }

  public function touch() : void {
    $this->touched = true;
  }

  public function value(...$optional) {
    $options = [
      'enforce_value' => false,
      'required' => null
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

    if($options['required'] !== null) {
      $options['enforce_value'] = $options['required'];
    }

    $this->touched = true;

    if($this->value !== null) {
      if($loader) {
        try {
          // TODO: What to do with $context, method signature, etc.?
          return $loader($this->context, $this->name, $this->value);
        } catch(Exception $e) {
          throw Validation::valueError($this->context, $e->getMessage(), $this->instruction);
        }
      }

      return $this->value;
    } else {
      if($options['enforce_value']) {
        throw Validation::missingValue($this->context, $this->instruction);
      }

      return null;
    }
  }
}
