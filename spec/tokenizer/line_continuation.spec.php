<?php declare(strict_types=1);

require_once(__DIR__ . '/util.php');

describe('Line continuation tokenization', function() {
  given('input', function() {
    return <<<DOC
\\value
\\ value
\\    value
    \\ value
    \\    value
DOC;
  });

  it('works as specified', function() {
    $instructions = inspectTokenization($this->input);

    expect($instructions)->toMatchSnapshot('spec/tokenizer/snapshots/line_continuation.snap.json');
  });
});
