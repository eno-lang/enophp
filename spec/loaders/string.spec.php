<?php declare(strict_types=1);

use Eno\Parser;

describe('string alias pseudo loader', function() {
  beforeAll(function() {
    $input = <<<DOC
field: value

list:
- value
- value

fieldset:
entry = value
DOC;

    $this->document = Parser::parse($input);
  });

  describe('as Fieldset entry proxy', function() {
    it('returns the value unaltered', function() {
      expect($this->document->fieldset('fieldset')->string('entry'))->toBe('value');
    });
  });

  describe('as List items proxy', function() {
    it('returns the values unaltered', function() {
      $values = $this->document->element('list')->stringItems();

      foreach($values as $value) {
        expect($value)->toBe('value');
      }
    });
  });

  describe('as Section field proxy', function() {
    it('returns the value unaltered', function() {
      expect($this->document->string('field'))->toBe('value');
    });
  });

  describe('as Section list proxy', function() {
    it('returns the values unaltered', function() {
      $values = $this->document->stringList('list');

      foreach($values as $value) {
        expect($value)->toBe('value');
      }
    });
  });

  describe('as Value value proxy', function() {
    it('returns the value unaltered', function() {
      expect($this->document->element('field')->string())->toBe('value');
    });
  });
});
