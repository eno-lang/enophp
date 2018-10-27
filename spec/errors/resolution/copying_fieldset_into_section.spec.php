<?php

use Eno\{Error, Parser};

describe('Resolution::copyingFieldsetIntoSection', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
original:
entry = value

# copy < original
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/resolution/snapshots/copying_fieldset_into_section.snap.error');
  });
});