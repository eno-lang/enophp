<?php declare(strict_types=1);

namespace Eno;
use \DateTime;
use \DateTimeZone;
use \Exception;

class Loaders {
  private const COLOR_REGEXP = '/^\s*#[0-9a-f]{3}([0-9a-f]{3})?\s*$/i';
  private const DATE_REGEXP = '/^\s*(\d{4})-(\d\d)-(\d\d)\s*$/';
  private const DATETIME_REGEXP = '/^\s*(\d{4})(?:-(\d\d)(?:-(\d\d)(?:T(\d\d):(\d\d)(?::(\d\d)(?:\.(\d+))?)?(?:(Z)|([+\-])(\d\d):(\d\d)))?)?)?\s*$/';
  private const EMAIL_REGEXP = '/^\s*[^@\s]+@[^@\s]+\.[^@\s]+\s*$/';
  private const FLOAT_REGEXP = '/^\s*-?\d+(\.\d+)?\s*$/';
  private const INTEGER_REGEXP = '/^\s*-?\d+\s*$/';
  private const LAT_LNG_REGEXP = '/^\s*(-?\d{1,3}(?:\.\d+)?)\s*,\s*(-?\d{1,3}(?:\.\d+)?)\s*$/';
  private const URL_REGEXP = '/^\s*https?:\/\/[^\s.]+\.\S+\s*$/';

  public static function boolean($name, $value, $context) {
    $lower = strtolower(trim($value));

    if($lower === 'true') return true;
    if($lower === 'false') return false;
    if($lower === 'yes') return true;
    if($lower === 'no') return false;

    throw new Exception($context->messages['loaders']['invalid_boolean']($name));
  }

  public static function color($name, $value, $context) {
    if(!preg_match(self::COLOR_REGEXP, $value)) {
      throw new Exception($context->messages['loaders']['invalid_color']($name));
    }

    return $value;
  }

  public static function commaSeparated($value) {
    return array_map(
      function($item) { return trim($item); },
      explode(',', $value)
    );
  }

  public static function date($name, $value, $context) {
    $matched = preg_match(self::DATE_REGEXP, $value, $match);

    if(!$matched) {
      throw new Exception($context->messages['loaders']['invalid_date']($name));
    }

    $date_time = new DateTime('0000-00-00', new DateTimeZone('UTC'));
    $date_time->setDate(
      intval($match[1]),
      intval($match[2]),
      intval($match[3])
    );

    return $date_time;
  }

  // Format specification thankfully taken from https://www.w3.org/TR/NOTE-datetime
  //
  // 1997
  // 1997-07
  // 1997-07-16
  // 1997-07-16T19:20+01:00
  // 1997-07-16T19:20:30+01:00
  // 1997-07-16T19:20:30.45+01:00
  // 1994-11-05T08:15:30-05:00
  // 1994-11-05T13:15:30Z
  public static function datetime($name, $value, $context) {
    $matched = preg_match(self::DATETIME_REGEXP, $value, $match, PREG_UNMATCHED_AS_NULL);

    if(!$matched) {
      throw new Exception($context->messages['loaders']['invalid_datetime']($name));
    }


    $timezone_sign = isset($match[9]) ? $match[9] : '+';
    $timezone_hour = isset($match[10]) ? $match[10] : '00';
    $timezone_minutes = isset($match[11]) ? $match[11] : '00';
    $timezone = new DateTimeZone(
      isset($match[8]) ? '+0000' : "{$timezone_sign}{$timezone_hour}{$timezone_minutes}"
    );

    $date_time = new DateTime('0000-00-00', $timezone);

    $date_time->setDate(
      intval($match[1]),
      isset($match[2]) ? intval($match[2]) : 1,
      isset($match[3]) ? intval($match[3]) : 1
    );
    $date_time->setTime(
      isset($match[4]) ? intval($match[4]) : 0,
      isset($match[5]) ? intval($match[5]) : 0,
      isset($match[6]) ? intval($match[6]) : 0,
      isset($match[7]) ? (int)(floatval("0.{$match[7]}") * 1000000) : 0
    );

    return $date_time;
  }

  public static function email($name, $value, $context) {
    if(!preg_match(self::EMAIL_REGEXP, $value)) {
      throw new Exception($context->messages['loaders']['invalid_email']($name));
    }

    return $value;
  }

  public static function float($name, $value, $context) {
    if(!preg_match(self::FLOAT_REGEXP, $value)) {
      throw new Exception($context->messages['loaders']['invalid_float']($name));
    }

    return floatval($value);
  }

  public static function integer($name, $value, $context) {
    if(!preg_match(self::INTEGER_REGEXP, $value)) {
      throw new Exception($context->messages['loaders']['invalid_integer']($name));
    }

    return intval($value);
  }

  public static function json($name, $value, $context) {
    $decoded = json_decode($value);

    switch(json_last_error()) {
      case JSON_ERROR_NONE:
          return $decoded;
      case JSON_ERROR_DEPTH:
        $error = 'Maximum stack depth exceeded';
        break;
      case JSON_ERROR_STATE_MISMATCH:
        $error = 'Underflow or the modes mismatch';
        break;
      case JSON_ERROR_CTRL_CHAR:
        $error = 'Unexpected control character found';
        break;
      case JSON_ERROR_SYNTAX:
        $error = 'Syntax error, malformed JSON';
        break;
      case JSON_ERROR_UTF8:
        $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
      default:
        $error = 'Unknown error';
        break;
    }

    throw new Exception($context->messages['loaders']['invalid_json']($name, $error));
  }

  public static function latLng($name, $value, $context) {
    $matched = preg_match(self::LAT_LNG_REGEXP, $value, $match);

    if(!$matched) {
      throw new Exception($context->messages['loaders']['invalid_lat_lng']($name));
    }

    return [ 'lat' => floatval($match[1]), 'lng' => floatval($match[2]) ];
  }

  public static function number($name, $value, $context) {
    return self::integer($name, $value, $context);
  }

  public static function url($name, $value, $context) {
    if(!preg_match(self::URL_REGEXP, $value)) {
      throw new Exception($context->messages['loaders']['invalid_url']($name));
    }

    return $value;
  }
}
