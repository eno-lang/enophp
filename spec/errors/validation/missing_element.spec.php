<?php declare(strict_types=1);

use Eno\{Parser};

describe('Validation::missingElement', function() {
  beforeAll(function() {
    $this->error = interceptValidationError(function() {
      $document = Parser::parse('# emptyness');
      $document->section('emptyness')->element('presence');
    });
  });

  it('throws the expected error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/missing_element.snap.error');
  });
});
