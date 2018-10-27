<?php

use Eno\{Error, Parser};

describe('Resolution::cyclicDependency', function() {
  given('scenarios', function() {
    return [
      'A' => <<<DOC
a < b
- 1
- 2

b < a
- 1
- 2
DOC
,
      'B' => <<<DOC
all < one
| all

one < two
| one

two < one
| two
DOC
,
      'C' => <<<DOC
either < or
either = or

or < either
or = either
DOC
,
      'D' => <<<DOC
# one < two
1: one

## three < four
3: three

# two < one
2: two

## four < one
4: four
DOC
,
      'E' => <<<DOC
# foo
## bar < foo
# baz < foo
DOC
,
      'F' => <<<DOC
# a
## b
### c < a
DOC
    ];
  });

  foreach($this->scenarios as $label => $input) {
    describe("scenario {$label}", function() use($label, $input) {
      it('provides a correct error', function() use($label, $input) {
        $error = interceptParseError(function() use($input) {
          Parser::parse($input);
        });

        expect($error)->toMatchErrorSnapshot("spec/errors/resolution/snapshots/cyclic_dependency_{$label}.snap.error");
      });
    });
  }
});
