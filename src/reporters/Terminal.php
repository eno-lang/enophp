<?php

namespace Eno\Reporters;

class Terminal implements Reporter  {
  static public function report(&$context, &$emphasized = [], &$marked = []) {
    if(count($emphasized) > 0) {
      if(isset($emphasized[0])) {
        $emphasized_arr = $emphasized;
      } else {
        $emphasized_arr = [$emphasized];
      }
    } else {
      $emphasized_arr = [];
    }

    if(count($marked) > 0) {
      if(isset($marked[0])) {
        $marked_arr = $marked;
      } else {
        $marked_arr = [$marked];
      }
    } else {
      $marked_arr = [];
    }

    $content_header = $context['messages']['reporting']['content_header'];
    $gutter_header = str_pad($context['messages']['reporting']['gutter_header'], 5, ' ', STR_PAD_LEFT);

    $gutter_width = strlen($gutter_header) + 3;
    $columns_header = "\x1b[1m  {$gutter_header} | {$content_header}\x1b[0m\n";
    $omission = "\x1b[1m" . str_repeat(' ', $gutter_width - 5) . "...\x1b[0m\n";

    $snippet = '';

    if(isset($context['source_label'])) {
      $snippet .= "\x1b[1m{$context['source_label']}\x1b[0m\n";
    }
    $snippet .= $columns_header;

    $in_omission = false;

    foreach($context['instructions'] as $instruction) {
      $emphasize = in_array($instruction, $emphasized_arr);
      $mark = in_array($instruction, $marked_arr);

      $show = false;
      foreach(array_merge($emphasized_arr, $marked_arr) as $marked_instruction) {
        if($instruction['line'] >= $marked_instruction['line'] - 2 &&
           $instruction['line'] <= $marked_instruction['line'] + 2) {
          $show = true;
          break;
        }
      }

      if($show) {
        $content = substr($context['input'], $instruction['index'], $instruction['length']);
        $number = (string)($instruction['line'] + $context['indexing']);

        if($emphasize) {
          $snippet .= "\x1b[41m >" . str_pad($number, $gutter_width - 3, ' ', STR_PAD_LEFT) . " | {$content}\x1b[0m\n";
        } else if($mark) {
          $snippet .= "\x1b[33m *" . str_pad($number, $gutter_width - 3, ' ', STR_PAD_LEFT) . " | {$content}\x1b[0m\n";
        } else {
          $snippet .= str_pad($number, $gutter_width - 1, ' ', STR_PAD_LEFT) . " | {$content}\n";
        }

        $in_omission = false;
      } else if(!$in_omission) {
        $snippet .= $omission;
        $in_omission = true;
      }
    }

    return $snippet;
  }
}
