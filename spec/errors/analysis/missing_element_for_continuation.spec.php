<?php

use Eno\{Error, Parser};

describe('Analysis::missingElementForContinuation', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
| eno
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/analysis/snapshots/missing_element_for_continuation.snap.error');
  });
});
