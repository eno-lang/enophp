<?php

namespace Eno\Matchers;

// TODO: Consider outputting a "xxx.new_diff" file if the snapshot does not match the new comparison value to be able
//       to do a quick diff with meld or similar (to augment the weak built-in diff from kahlan's CLI reporter)

class ToMatchSnapshot
{
  public static function match($actual, $expected)
  {
    $extension = pathinfo($expected, PATHINFO_EXTENSION);
    $snapshot = @file_get_contents($expected);

    // When comparing object vs. object through JSON serialization
    // we do the comparison not with objects but with the serialized
    // json string, because when serializing back into php from json
    // we get different objects than the ones used for serialization
    // and the comparison is thus always unequal even for identical data.
    // (or in other words: roundtrip serialization does not work in php)
    if($extension === "json") {
      $actual = json_encode($actual, JSON_PRETTY_PRINT);
    }

    if($snapshot === false) {
      file_put_contents($expected, $actual);

      return true;
    } else {
      return $actual === $snapshot;
    }
  }

  public static function description()
  {
    return "match the snapshot.";
  }
}
