<?php

use Eno\Parser;

describe('commaSeparated loader', function() {
  $examples = [
    'one,two,three'     => ['one', 'two', 'three'],
    'one , two , three' => ['one', 'two', 'three'],
    ',,'                => ['', '', ''],
    'one two three'     => ['one two three'],
    'one;two;three'     => ['one;two;three']
  ];

  foreach($examples as $value => $result) {
    describe($value, function() use($value, $result) {
      beforeAll(function() use($value) {
        $this->document = Parser::parse("value: {$value}");
      });

      it('returns the expected array', function() use($result) {
        expect($this->document->commaSeparated('value'))->toBe($result);
      });
    });
  }
});
