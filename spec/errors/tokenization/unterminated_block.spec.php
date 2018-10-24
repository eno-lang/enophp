<?php

use Eno\{ParseError, Parser};

describe('Tokenization::unterminatedBlock', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
-- languages
eno
json
yaml
- languages
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/tokenization/snapshots/unterminated_block.snap.error');
  });
});
