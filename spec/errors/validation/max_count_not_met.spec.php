<?php declare(strict_types=1);

use Eno\{Parser};

describe('Validation::maxCountNotMet', function() {
  beforeAll(function() {
    $this->error = interceptValidationError(function() {
      $input = <<<DOC
languages:
- cson
- eno
- json
- yaml
DOC;

      $document = Parser::parse($input);
      $document->list('languages', [ 'max_count' => 3 ]);
    });
  });

  it('throws the expected error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/max_count_not_met.snap.error');
  });
});
