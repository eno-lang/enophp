<?php

use Eno\{Error, Parser};

describe('Tokenization::unterminatedEscapedName', function() {
  beforeAll(function() {
    $input = <<<DOC
`language: eno
DOC;

    try {
      Parser::parse($input);
    } catch(Error $e) {
      $this->error = $e;
    }
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/tokenization/snapshots/unterminated_escaped_name.snap.error');
  });
});
