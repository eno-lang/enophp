<?php

use Eno\{Parser};

describe('Validation::missingList', function() {
  beforeAll(function() {
    $this->error = interceptValidationError(function() {
      $document = Parser::parse('# emptyness');
      $document->section('emptyness')->list('presence', [ 'enforce_element' => true ]);
    });
  });

  it('throws the expected error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/missing_list.snap.error');
  });
});
