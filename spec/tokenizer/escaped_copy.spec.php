<?php

require_once(__DIR__ . '/util.php');

describe('Escaped copy tokenization', function() {
  given('input', function() {
    return <<<DOC
`name` < template
``na`me`` < template
```na``me```    < template
    `` `name` ``    < template
DOC;
  });

  it('works as specified', function() {
    $instructions = inspectTokenization($this->input);

    expect($instructions)->toMatchSnapshot('spec/tokenizer/snapshots/escaped_copy.snap.json');
  });
});
