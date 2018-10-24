<?php

require_once(__DIR__ . '/util.php');

describe('Block tokenization', function() {
  given('input', function() {
    return <<<DOC
-- name
value
-- name

--    name

value

    -- name

    --    name
value

    value
        -- name
DOC;
  });

  it('works as specified', function() {
    $instructions = inspectTokenization($this->input);

    expect($instructions)->toMatchSnapshot('spec/tokenizer/snapshots/block.snap.json');
  });
});
