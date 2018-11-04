<?php declare(strict_types=1);

use Eno\{Parser};

describe('Validation::exactCountNotMet', function() {
  beforeAll(function() {
    $this->error = interceptValidationError(function() {
      $input = <<<DOC
languages:
- eno
- json
DOC;

      $document = Parser::parse($input);
      $document->list('languages', [ 'exact_count' => 5 ]);
    });
  });

  it('throws the expected error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/exact_count_not_met.snap.error');
  });
});
