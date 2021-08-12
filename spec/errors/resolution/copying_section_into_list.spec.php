<?php declare(strict_types=1);

use Eno\{Errors\Error, Parser};

describe('Resolution::copyingSectionIntoList', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
# original

# other

copy < original
- item
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/resolution/snapshots/copying_section_into_list.snap.error');
  });
});
