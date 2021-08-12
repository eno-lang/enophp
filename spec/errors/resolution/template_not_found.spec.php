<?php declare(strict_types=1);

use Eno\{Errors\Error, Parser};

describe('Resolution::templateNotFound', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
copy < original
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/resolution/snapshots/template_not_found.snap.error');
  });
});
