<?php declare(strict_types=1);

use Eno\Parser;

describe('latLng loader', function() {
  $examples = [
    '48.205870, 16.413690' => [ 'lat' => 48.205870, 'lng' => 16.413690 ],
    '41.25, -120.9762'     => [ 'lat' => 41.25,     'lng' => -120.9762 ],
    '-31.96, 115.84'       => [ 'lat' => -31.96,    'lng' => 115.84 ],
    '90, 0'                => [ 'lat' => 90,        'lng' => 0 ],
    '   0   ,   0   '      => [ 'lat' => 0,         'lng' => 0 ],
    '-0,-0'                => [ 'lat' => -0,        'lng' => -0 ],
    '1000,10'              => false,
    '10,1000'              => false,
    '48.205870,'           => false,
    ', 16.413690'          => false,
    '48,205870, 16,413690' => false
  ];

  foreach($examples as $value => $result) {
    describe($value, function() use($value, $result) {
      beforeAll(function() use($value) {
        $this->document = Parser::parse("value: {$value}");
      });

      if($result === false) {
        it('throws the expected error', function() use($value) {
          $error = interceptValidationError(function() {
            $this->document->latLng('value');
          });

          expect($error)->toMatchErrorSnapshot("spec/loaders/snapshots/lat_lng_{$value}.snap.error");
        });
      } else {
        it('returns the expected object', function() use($result) {
          expect($this->document->latLng('value'))->toEqual($result);
        });
      }
    });
  }
});
