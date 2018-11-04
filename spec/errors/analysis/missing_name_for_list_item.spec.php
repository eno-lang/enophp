<?php declare(strict_types=1);

use Eno\{Error, Parser};

describe('Analysis::missingNameForListItem', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
- eno
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/analysis/snapshots/missing_name_for_list_item.snap.error');
  });
});
