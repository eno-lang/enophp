<?php

require_once(__DIR__ . '/util.php');

describe('Section tokenization', function() {
  given('input', function() {
    return <<<DOC
# name
    ## name
###    name
    ####    name
DOC;
  });

  it('works as specified', function() {
    $instructions = inspectTokenization($this->input);

    expect($instructions)->toMatchSnapshot('spec/tokenizer/snapshots/section.snap.json');
  });
});
