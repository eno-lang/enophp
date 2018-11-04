<?php declare(strict_types=1);

use Eno\{Parser};

describe('Validation::excessName', function() {
  beforeAll(function() {
    $this->document = Parser::parse('language: eno');
  });

  describe('without a custom message', function() {
    beforeAll(function() {
      $this->error = interceptValidationError(function() {
        $this->document->assertAllTouched();
      });
    });

    it('throws the expected error', function() {
      expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/excess_name_without_a_custom_message.snap.error');
    });
  });

  describe('with a custom message', function() {
    beforeAll(function() {
      $this->error = interceptValidationError(function() {
        $this->document->assertAllTouched('my custom message');
      });
    });

    it('throws the expected error', function() {
      expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/excess_name_with_a_custom_message.snap.error');
    });
  });
});
