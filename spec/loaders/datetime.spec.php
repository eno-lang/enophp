<?php

use Eno\Parser;

describe('datetime loader', function() {
  $examples = [
    '1990'                         => '1990-01-01T00:00:00.000000+00:00',
    '1991-01'                      => '1991-01-01T00:00:00.000000+00:00',
    '1992-02-02'                   => '1992-02-02T00:00:00.000000+00:00',
    '1993-03-03T19:20+01:00'       => '1993-03-03T19:20:00.000000+01:00',
    '1994-04-04T19:20:30+01:00'    => '1994-04-04T19:20:30.000000+01:00',
    '1995-05-05T19:20:30.45+01:00' => '1995-05-05T19:20:30.450000+01:00',
    '1996-06-06T08:15:30-05:00'    => '1996-06-06T08:15:30.000000-05:00',
    '1997-07-07T13:15:30Z'         => '1997-07-07T13:15:30.000000+00:00',
    '2002 12 14'                   => false,
    '2002-12-14 20:15'             => false,
    'January'                      => false,
    '13:00'                        => false
  ];

  foreach($examples as $value => $result) {
    describe($value, function() use($value, $result) {
      beforeAll(function() use($value) {
        $this->document = Parser::parse("value: {$value}");
      });

      if($result === false) {
        it('throws the expected error', function() use($value) {
          $error = interceptValidationError(function() {
            $this->document->datetime('value');
          });

          expect($error)->toMatchErrorSnapshot("spec/loaders/snapshots/datetime_{$value}.snap.error");
        });
      } else {
        it("returns {$result}", function() use($result) {
          expect($this->document->datetime('value')->format('Y-m-d\TH:i:s.uP'))->toBe($result);
        });
      }
    });
  }
});
