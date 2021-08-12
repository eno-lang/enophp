<?php declare(strict_types=1);

use Eno\{Errors\Error, Parser};

describe('Resolution::copyingFieldIntoFieldset', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
original: value

copy < original
entry = value
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/resolution/snapshots/copying_field_into_fieldset.snap.error');
  });
});
