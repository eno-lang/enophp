<?php

use Eno\{Error, Parser};

describe('Resolution::copyingFieldIntoSection', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
original: value

# copy < original
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/resolution/snapshots/copying_field_into_section.snap.error');
  });
});
