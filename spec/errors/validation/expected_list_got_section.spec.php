<?php declare(strict_types=1);

use Eno\{Parser};

describe('Validation::expectedListGotSection', function() {
  beforeAll(function() {
    $this->error = interceptValidationError(function() {
      $input = <<<DOC
# languages
eno: eno notation
yaml: yaml ain't markup language
DOC;

      $document = Parser::parse($input);
      $document->list('languages');
    });
  });

  it('throws the expected error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/validation/snapshots/expected_list_got_section.snap.error');
  });
});
