<?php declare(strict_types=1);

use Eno\{Parser};

describe('Validation::missingFieldsetEntry', function() {
  beforeAll(function() {
    $this->document = Parser::parse('emptyness:');
  });

  describe('through element() on Fieldset', function() {
    beforeAll(function() {
      $this->error = interceptValidationError(function() {
        $this->document->fieldset('emptyness')->element('presence', [ 'enforce_element' => true ]);
      });
    });

    it('throws the expected error', function() {
      expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/missing_fieldset_entry_through_element_accessor.snap.error');
    });
  });

  describe('through entry() on Fieldset', function() {
    beforeAll(function() {
      $this->error = interceptValidationError(function() {
        $this->document->fieldset('emptyness')->entry('presence', [ 'enforce_element' => true ]);
      });
    });

    it('throws the expected error', function() {
      expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/missing_fieldset_entry_through_entry_accessor.snap.error');
    });
  });
});
