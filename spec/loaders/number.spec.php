<?php

use Eno\Parser;

describe('number loader', function() {
  $examples = [
    '42'       => 42,
    '-42'      => -42,
    '42.0'     => false,
    '42,0'     => false,
    '4 2'      => false,
    'fortytwo' => false
  ];

  foreach($examples as $value => $result) {
    describe($value, function() use($value, $result) {
      beforeAll(function() use($value) {
        $this->document = Parser::parse("value: {$value}");
      });

      if($result === false) {
        it('throws the expected error', function() use($value) {
          $error = interceptValidationError(function() {
            $this->document->number('value');
          });

          expect($error)->toMatchErrorSnapshot("spec/loaders/snapshots/number_{$value}.snap.error");
        });
      } else {
        it("returns {$result}", function() use($result) {
          expect($this->document->number('value'))->toBe($result);
        });
      }
    });
  }
});
