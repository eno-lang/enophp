<?php declare(strict_types=1);

use Eno\Parser;

describe('url loader', function() {
  $examples = [
    'http://www.valid.com'  => 'http://www.valid.com',
    'https://valid.com'     => 'https://valid.com',
    'https://www.valid.com' => 'https://www.valid.com',
    'invalid'               => false,
    'www.invalid'           => false,
    'www.invalid.com'       => false,
    'htp://www.invalid.com' => false,
    'http:/invalid.com'     => false,
    'https//invalid.com'    => false,
    'https://invalid'       => false
  ];

  foreach($examples as $value => $result) {
    describe($value, function() use($value, $result) {
      beforeAll(function() use($value) {
        $this->document = Parser::parse("value: {$value}");
      });

      if($result === false) {
        it('throws the expected error', function() use($value) {
          $error = interceptValidationError(function() {
            $this->document->url('value');
          });

          $escaped = preg_replace('/[^A-Za-z0-9_]/', '_', $value);
          expect($error)->toMatchErrorSnapshot("spec/loaders/snapshots/url_{$escaped}.snap.error");
        });
      } else {
        it("returns {$result}", function() use($result) {
          expect($this->document->url('value'))->toBe($result);
        });
      }
    });
  }
});
