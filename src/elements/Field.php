<?php

namespace Eno;

class Field {
  public $touched;

  function __construct($context, $instruction, $parent, $from_empty = false) {
    $this->context = $context;
    $this->instruction = $instruction;
    $this->name = $instruction->name;
    $this->parent = $parent;
    $this->value = $instruction->value;
    $this->touched = false;

    if($from_empty)
      return;

    $instruction->element = $this;

    if($instruction->type == 'BLOCK' && array_key_exists('content_range', $instruction)) {
      $this->value = substr($context->input, $instruction['content_range'][0], $instruction['content_range'][1] + 1);

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

  public function isEmpty() {
    return $this->value === null;
  }

  public function raw() {
    if($this->name === null) {
      return $this->value;
    } else {
      return [ $this->name => $this->value ];
    }
  }

  public function touch() {
    $this->touched = true;
  }

  public function value() {
    return $this->value;
    // TODO ...
  }
}
