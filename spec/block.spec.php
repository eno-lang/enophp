<?php declare(strict_types=1);

describe('Block value construction', function() {
  it('retrieves the correct substring range', function() {
    $input = "-- multiline_field\n" .
             "value\n" .
             "-- multiline_field";

    $output = Eno\Parser::parse($input)->string('multiline_field');

    $expected = "value";

    expect($output)->toEqual($expected);
  });
});

describe('Copied block value construction', function() {
  it('retrieves the correct substring range', function() {
    $input = "-- multiline_field\n" .
             "value\n" .
             "-- multiline_field\n" .
             "copy < multiline_field";

    $output = Eno\Parser::parse($input)->string('copy');

    $expected = "value";

    expect($output)->toEqual($expected);
  });
});
