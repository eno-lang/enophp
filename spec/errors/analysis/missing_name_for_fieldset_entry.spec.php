<?php

use Eno\{Error, Parser};

describe('Analysis::missingNameForFieldsetEntry', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
eno = eno notation
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/analysis/snapshots/missing_name_for_fieldset_entry.snap.error');
  });
});
