<?php declare(strict_types=1);

use Eno\{Error, Parser};

describe('Analysis::sectionHierarchyLayerSkip', function() {
  beforeAll(function() {
    $this->error = interceptParseError(function() {
      $input = <<<DOC
# section
### subsection
DOC;

      Parser::parse($input);
    });
  });

  it('provides a correct error', function() {
    expect($this->error)->toMatchErrorSnapshot('spec/errors/analysis/snapshots/section_hierarchy_layer_skip.snap.error');
  });
});
