<?php

require_once(__DIR__ . '/util.php');

describe('Escaped fieldset entry tokenization', function() {
  given('input', function() {
    return <<<DOC
`name` =
``na`me`` =
```na``me```    =
    `` `name` ``    =
DOC;
  });

  it('works as specified', function() {
    $instructions = inspectTokenization($this->input);

    expect($instructions)->toMatchSnapshot('spec/tokenizer/snapshots/escaped_fieldset_entry.snap.json');
  });
});
