<?php

use Eno\Parser;

describe('color loader', function() {
  $examples = [
    '#abcdef' => '#abcdef',
    '#ABCDEF' => '#ABCDEF',
    '#012345' => '#012345',
    '#678'    => '#678',
    '#89a'    => '#89a',
    '#ab'     => false,
    '#abcd'   => false,
    '#abcde'  => false,
    '#bcdefg' => false,
    'blue'    => false
  ];

  foreach($examples as $value => $result) {
    describe($value, function() use($value, $result) {
      beforeAll(function() use($value) {
        $this->document = Parser::parse("value: {$value}");
      });

      if($result === false) {
        it('throws the expected error', function() use($value) {
          $error = interceptValidationError(function() {
            $this->document->color('value');
          });

          expect($error)->toMatchErrorSnapshot("spec/loaders/snapshots/color_{$value}.snap.error");
        });
      } else {
        it("returns {$result}", function() use($result) {
          expect($this->document->color('value'))->toBe($result);
        });
      }
    });
  }
});
