<?php declare(strict_types=1);

use Eno\Parser;

describe('date loader', function() {
  $examples = [
    '1992-02-02'                   => '1992-02-02T00:00:00+0000',
    '1990'                         => false,
    '1991-01'                      => false,
    '1993-03-03T19:20+01:00'       => false,
    '1994-04-04T19:20:30+01:00'    => false,
    '1995-05-05T19:20:30.45+01:00' => false,
    '1996-06-06T08:15:30-05:00'    => false,
    '1997-07-07T13:15:30Z'         => false,
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
            $this->document->date('value');
          });

          expect($error)->toMatchErrorSnapshot("spec/loaders/snapshots/date_{$value}.snap.error");
        });
      } else {
        it("returns {$result}", function() use($result) {
          expect($this->document->date('value')->format(DateTime::ISO8601))->toBe($result);
        });
      }
    });
  }
});
