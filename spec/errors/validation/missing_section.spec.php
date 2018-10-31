<?php

use Eno\{Parser};

describe('Validation::missingSection', function() {
  beforeAll(function() {
    $this->error = interceptValidationError(function() {
      $document = Parser::parse('# emptyness');
      $document->section('emptyness')->section('presence');
    });
  });

  it('throws the expected error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/missing_section.snap.error');
  });
});
