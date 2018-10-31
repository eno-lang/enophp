<?php

use Eno\{Parser};

describe('Validation::minCountNotMet', function() {
  beforeAll(function() {
    $this->error = interceptValidationError(function() {
      $input = <<<DOC
languages:
- eno
- json
DOC;

      $document = Parser::parse($input);
      $document->list('languages', [ 'min_count' => 3 ]);
    });
  });

  it('throws the expected error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/min_count_not_met.snap.error');
  });
});
