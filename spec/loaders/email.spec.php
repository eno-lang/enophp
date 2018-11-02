<?php

use Eno\Parser;

describe('email loader', function() {
  $examples = [
    'john.doe@eno-lang.org' => 'john.doe@eno-lang.org',
    'john.doe@eno-lang'     => false,
    '@eno-lang.org'         => false,
    'john.doe@.org'         => false
  ];

  foreach($examples as $value => $result) {
    describe($value, function() use($value, $result) {
      beforeAll(function() use($value) {
        $this->document = Parser::parse("value: {$value}");
      });

      if($result === false) {
        it('throws the expected error', function() use($value) {
          $error = interceptValidationError(function() {
            $this->document->email('value');
          });

          expect($error)->toMatchErrorSnapshot("spec/loaders/snapshots/email_{$value}.snap.error");
        });
      } else {
        it("returns {$result}", function() use($result) {
          expect($this->document->email('value'))->toBe($result);
        });
      }
    });
  }
});
