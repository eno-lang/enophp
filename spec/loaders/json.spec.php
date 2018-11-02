<?php

use Eno\Parser;

describe('json loader', function() {
  $examples = [
    '{ "valid": true }'    => (object) [ 'valid' => true ],
    '42'                   => 42,
    '["valid", true]'      => ['valid', true],
    'invalid'              => false,
    '{ invalid: true }'    => false,
    '{ "invalid": true, }' => false
  ];

  foreach($examples as $value => $result) {
    describe($value, function() use($value, $result) {
      beforeAll(function() use($value) {
        $this->document = Parser::parse("value: {$value}");
      });

      if($result === false) {
        it('throws the expected error', function() use($value) {
          $error = interceptValidationError(function() {
            $this->document->json('value');
          });

          expect($error)->toMatchErrorSnapshot("spec/loaders/snapshots/json_{$value}.snap.error");
        });
      } else {
        it('returns the expected object', function() use($result) {
          expect($this->document->json('value'))->toEqual($result);
        });
      }
    });
  }
});
