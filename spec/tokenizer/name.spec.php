<?php declare(strict_types=1);

require_once(__DIR__ . '/util.php');

describe('Name tokenization', function() {
  given('input', function() {
    return <<<DOC
name:
    name:
name    :
    name    :
DOC;
  });

  it('works as specified', function() {
    $instructions = inspectTokenization($this->input);

    expect($instructions)->toMatchSnapshot('spec/tokenizer/snapshots/name.snap.json');
  });
});
