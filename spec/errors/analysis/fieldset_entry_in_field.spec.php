<?php declare(strict_types=1);

use Eno\{Errors\Error, Parser};

describe('Analysis::fieldsetEntryInField', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
language:
| eno
json = JavaScript Object Notation
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/analysis/snapshots/fieldset_entry_in_field.snap.error');
  });
});
