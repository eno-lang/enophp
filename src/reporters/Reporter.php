<?php

namespace Eno\Reporters;
use \stdClass;

interface Reporter {
  public static function report(stdClass $context, $emphasized = [], $marked = []) : string;
}
