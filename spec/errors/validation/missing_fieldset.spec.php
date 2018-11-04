<?php declare(strict_types=1);

use Eno\{Parser};

describe('Validation::missingFieldset', function() {
  beforeAll(function() {
    $this->error = interceptValidationError(function() {
      $document = Parser::parse('# emptyness');
      $document->section('emptyness')->fieldset('presence');
    });
  });

  it('throws the expected error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/missing_fieldset.snap.error');
  });
});
