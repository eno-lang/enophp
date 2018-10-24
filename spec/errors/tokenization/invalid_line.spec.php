<?php

use Eno\{ParseError, Parser};

describe('Tokenization::invalidLine', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
languages:
- eno
- json
yaml
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/tokenization/snapshots/invalid_line.snap.error');
  });
});
