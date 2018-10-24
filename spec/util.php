<?php

use Eno\{ParseError, ValidationError};

function interceptParseError($callback) {
  $error = null;

  try {
    $callback();
  } catch(ParseError $e) {
    $error = $e;
  }

  if($error === null) {
    throw new Exception('No ParseError was thrown, although it should have been!');
  }

  return $error;
}

function interceptValidationError($callback) {
  $error = null;

  try {
    $callback();
  } catch(ValidationError $e) {
    $error = $e;
  }

  if($error === null) {
    throw new Exception('No ValidationError was thrown, although it should have been!');
  }

  return $error;
}
