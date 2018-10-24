<?php

use Eno\{ParseError, Parser};

describe('Tokenization::invalidLine', function() {
  beforeAll(function() {
    $input = <<<DOC
languages:
- eno
- json
yaml
DOC;

    try {
      Parser::parse($input);
    } catch(ParseError $e) {
      $this->error = $e;
    }
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/tokenization/snapshots/invalid_line.snap.error');
  });
});
