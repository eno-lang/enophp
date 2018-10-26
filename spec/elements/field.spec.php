<?php

use Eno\Field;

describe('Field', function() {
  given('_context', function() { return (object) []; });
  given('instruction', function() { return (object) [ 'name' => 'language', 'value' => 'eno' ]; });
  given('instruction_named_valueless', function() { return (object) [ 'name' => 'language', 'value' => null ]; });
  given('instruction_unnamed_value', function() { return (object) [ 'name' => null, 'value' => 'eno' ]; });
  given('instruction_unnamed_long_value', function() { return (object) [ 'name' => null, 'value' => 'The language is eno' ]; });
  given('instruction_void', function() { return (object) [ 'name' => null, 'value' => null ]; });
  given('parent', function() { return (object) []; });

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
});

