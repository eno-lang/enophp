<?php declare(strict_types=1);

use Eno\{Errors\Error, Parser};

describe('Analysis::listItemInField', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
language:
| eno
- json
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/analysis/snapshots/list_item_in_field.snap.error');
  });
});
