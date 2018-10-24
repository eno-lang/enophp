<?php

namespace Eno\Matchers;

class ToMatchErrorSnapshot extends ToMatchSnapshot
{
  public static function match($actual, $snapshot_file)
  {
    $actual = "--- message\n" . $actual->message . "\n--- selection\n" . json_encode($actual->selection);

    return parent::match($actual, $snapshot_file);
  }
}
