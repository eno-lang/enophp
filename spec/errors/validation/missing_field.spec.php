<?php

use Eno\{Parser};

describe('Validation::missingField', function() {
  beforeAll(function() {
    $this->error = interceptValidationError(function() {
      $document = Parser::parse('# emptyness');
      $document->section('emptyness')->field('presence', [ 'enforce_element' => true ]);
    });
  });

  it('throws the expected error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/missing_field.snap.error');
  });
});
