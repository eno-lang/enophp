<?php declare(strict_types=1);

use Eno\{Errors\Error, Parser};

describe('Analysis::duplicateFieldsetEntryName', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
languages:
eno = eno notation
json = JavaScript Object Notation
eno = eno notation
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/analysis/snapshots/duplicate_fieldset_entry_name.snap.error');
  });
});
