<?php

use Eno\{Parser};

describe('Validation::expectedFieldsetGotSection', function() {
  beforeAll(function() {
    $this->error = interceptValidationError(function() {
      $input = <<<DOC
# languages
eno: eno notation
json: JavaScript Object Notation
DOC;

      $document = Parser::parse($input);
      $document->fieldset('languages');
    });
  });

  it('throws the expected error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/expected_fieldset_got_section.snap.error');
  });
});
