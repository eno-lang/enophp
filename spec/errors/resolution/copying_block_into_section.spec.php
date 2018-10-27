<?php

use Eno\{Error, Parser};

describe('Resolution::copyingBlockIntoSection', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
-- original
value
-- original

# copy < original
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/resolution/snapshots/copying_block_into_section.snap.error');
  });
});
