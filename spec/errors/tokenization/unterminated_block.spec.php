<?php

use Eno\{ParseError, Parser};

describe('Tokenization::unterminatedBlock', function() {
  beforeAll(function() {
    $input = <<<DOC
-- languages
eno
json
yaml
- languages
DOC;

    try {
      Parser::parse($input);
    } catch(ParseError $e) {
      $this->error = $e;
    }
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/tokenization/snapshots/unterminated_block.snap.error');
  });
});
