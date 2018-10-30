<?php

use Eno\{Field, Parser};

describe('Field', function() {
  beforeAll(function() {
    $this->_context = (object) [];
    $this->instruction = (object) [
      'name' => 'language',
      'subinstructions' => [],
      'type' => 'FIELD',
      'value' => 'eno'
    ];
    $this->instruction_named_valueless = (object) [
      'name' => 'language',
      'subinstructions' => [],
      'type' => 'FIELD',
      'value' => null
    ];
    $this->instruction_unnamed_value = (object) [
      'name' => null,
      'subinstructions' => [],
      'type' => 'FIELD',
      'value' => 'eno'
    ];
    $this->instruction_unnamed_long_value = (object) [
      'name' => null,
      'subinstructions' => [],
      'type' => 'FIELD',
      'value' => 'The language is eno'
    ];
    $this->instruction_void = (object) [
      'name' => null,
      'subinstructions' => [],
      'type' => 'FIELD',
      'value' => null
    ];
    $this->parent = (object) [];
  });


  beforeEach(function() {
    $this->field = new Field($this->_context, $this->instruction, $this->parent);
  });

  it('is untouched after initialization', function() {
    expect($this->field->touched)->toBe(false);
  });

  describe('isEmpty()', function() {
    describe('when not empty', function() {
      it('returns false', function() {
        expect($this->field->isEmpty())->toBe(false);
      });
    });

    describe('when empty', function() {
      it('returns true', function() {
        $named_empty_field = new Field($this->_context, $this->instruction_named_valueless, $this->parent);
        expect($named_empty_field->isEmpty())->toBe(true);
      });
    });
  });

  describe('raw()', function() {
    describe('with a name and a value', function() {
      it('returns a native representation', function() {
        expect($this->field->raw())->toEqual([ 'language' => 'eno' ]);
      });
    });

    describe('with an unnamed value', function() {
      it('returns a native representation', function() {
        $unnamed_field = new Field($this->_context, $this->instruction_unnamed_value, $this->parent);
        expect($unnamed_field->raw())->toEqual('eno');
      });
    });
  });

  describe('__toString()', function() {
    describe('with a name and a value', function() {
      it('returns a debug abstraction', function() {
        expect((string)$this->field)->toEqual('[Field name="language" value="eno"]');
      });
    });

    describe('with a name and no value', function() {
      it('returns a debug abstraction', function() {
        $named_empty_field = new Field($this->_context, $this->instruction_named_valueless, $this->parent);
        expect((string)$named_empty_field)->toEqual('[Field name="language" value=null]');
      });
    });

    describe('with an unnamed value', function() {
      it('returns a debug abstraction', function() {
        $unnamed_field = new Field($this->_context, $this->instruction_unnamed_value, $this->parent);
        expect((string)$unnamed_field)->toEqual('[Field value="eno"]');
      });
    });

    describe('with no name and value', function() {
      it('returns a debug abstraction', function() {
        $void_field = new Field($this->_context, $this->instruction_void, $this->parent);
        expect((string)$void_field)->toEqual('[Field value=null]');
      });
    });

    describe('with no name and a long value', function() {
      it('returns a debug abstraction with a truncated value', function() {
        $unnamed_long_value_field = new Field($this->_context, $this->instruction_unnamed_long_value, $this->parent);
        expect((string)$unnamed_long_value_field)->toEqual('[Field value="The languag..."]');
      });
    });
  });

  describe('value()', function() {
    describe('without a loader', function() {
      beforeEach(function() {
        $this->value = $this->field->value();
      });

      it('returns the value', function() {
        expect($this->value)->toEqual('eno');
      });

      it('touches the element', function() {
        expect($this->field->touched)->toBe(true);
      });
    });

    describe('with a loader closure', function() {
      beforeEach(function() {
        $this->result = $this->field->value(function($context, $name, $value) {
          return strtoupper($value);
        });
      });

      it('applies the loader', function() {
        expect($this->result)->toEqual('ENO');
      });

      it('touches the element', function() {
        expect($this->field->touched)->toBe(true);
      });
    });

    describe("'required' alias for 'enforceValue'", function() {
      beforeEach(function() {
        $input = <<<DOC
language:
|
DOC;

        $this->empty_field = Parser::parse($input)->element('language');
      });

      describe('when not set', function() {
        it('returns null', function() {
          expect($this->empty_field->value())->toBe(null);
        });
      });

      describe('when set to true', function() {
        it('throws an error', function() {
          $error = interceptValidationError(function() {
            $_ = $this->empty_field->value([ 'required' => true ]);
          });

          expect($error)->toMatchErrorSnapshot('spec/elements/snapshots/field_value_with_required.snap.error');
        });
      });
    });
  });
});
