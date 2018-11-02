<?php

use Eno\Parser;

describe('boolean loader', function() {
  $examples = [
    'true'  => true,
    'false' => false,
    'yes'   => true,
    'no'    => false,
    'nope'  => null
  ];

  foreach($examples as $value => $result) {
    describe($value, function() use($value, $result) {
      beforeAll(function() use($value) {
        $this->document = Parser::parse("value: {$value}");
      });

      if($result === null) {
        it('throws the expected error', function() use($value) {
          $error = interceptValidationError(function() {
            $this->document->boolean('value');
          });

          expect($error)->toMatchErrorSnapshot("spec/loaders/snapshots/boolean_{$value}.snap.error");
        });
      } else {
        it("returns {$result}", function() use($result) {
          expect($this->document->boolean('value'))->toBe($result);
        });
      }
    });
  }
});
