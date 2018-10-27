<?php

use Eno\{Error, Parser};

describe('Resolution::copyingSectionIntoEmpty', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
# original

# other

copy < original
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/resolution/snapshots/copying_section_into_empty.snap.error');
  });
});
