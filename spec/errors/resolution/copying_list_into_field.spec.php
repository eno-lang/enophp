<?php declare(strict_types=1);

use Eno\{Errors\Error, Parser};

describe('Resolution::copyingListIntoField', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
original:
- value

copy < original
| appendix
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/resolution/snapshots/copying_list_into_field.snap.error');
  });
});
