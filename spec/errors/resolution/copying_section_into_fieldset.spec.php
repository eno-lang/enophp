<?php declare(strict_types=1);

use Eno\{Error, Parser};

describe('Resolution::copyingSectionIntoFieldset', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
# original

# other

copy < original
entry = value
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/resolution/snapshots/copying_section_into_fieldset.snap.error');
  });
});
