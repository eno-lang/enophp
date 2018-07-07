<?php
  require_once('grammar.php');

  preg_match($REGEX, 'language: eno', $matches, PREG_UNMATCHED_AS_NULL);

  echo($matches[0]);
?>
