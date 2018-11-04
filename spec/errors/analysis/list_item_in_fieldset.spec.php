<?php declare(strict_types=1);

use Eno\{Error, Parser};

describe('Analysis::listItemInFieldset', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
languages:
eno = eno notation
- json
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/analysis/snapshots/list_item_in_fieldset.snap.error');
  });
});
