<?php

namespace Eno\Reporters;

interface Reporter {
  public static function report($context, $emphasized = [], $marked = []);
}
