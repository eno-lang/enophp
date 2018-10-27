<?php

use Eno\{Error, Parser};

describe('Resolution::copyingListIntoFieldset', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
original:
- value

copy < original
entry = value
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/resolution/snapshots/copying_list_into_fieldset.snap.error');
  });
});
