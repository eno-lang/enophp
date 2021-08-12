<?php declare(strict_types=1);

use Eno\{Errors\Error, Parser};

describe('Resolution::multipleTemplatesFound', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
original: value
original: value

copy < original
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/resolution/snapshots/multiple_templates_found.snap.error');
  });
});
