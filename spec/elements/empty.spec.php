<?php

use Eno\EmptyElement;

describe('EmptyElement', function() {
  given('_context', function() { return []; });
  given('instruction', function() { return [ 'name' => 'language' ]; });
  given('parent', function() { return []; });

  beforeEach(function() {
    $this->empty = new EmptyElement($this->_context, $this->instruction, $this->parent);
  });

  it('is untouched after initialization', function() {
    expect($this->empty->touched)->toBe(false);
  });

  describe('raw', function() {
    it('returns a native object representation', function() {
      expect($this->empty->raw())->toEqual([ 'language' => null ]);
    });
  });

  describe('__toString()', function() {
    it('returns a debug representation', function() {
      expect((string)$this->empty)->toEqual('[EmptyElement name="language"]');
    });
  });

  describe('touch()', function() {
    it('touches the element', function() {
      $this->empty->touch();
      expect($this->empty->touched)->toBe(true);
    });
  });

  describe('value()', function() {
    it('returns null', function() {
      expect($this->empty->value())->toBe(null);
    });

    it('touches the element', function() {
      $_ = $this->empty->value();
      expect($this->empty->touched)->toBe(true);
    });
  });
});
