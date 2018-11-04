<?php declare(strict_types=1);

namespace Eno\Reporters;
use \stdClass;

class Text implements Reporter {
  static public function report(stdClass $context, $emphasized = [], $marked = []) : string {
    if($emphasized instanceof stdClass) {
      $emphasized = [$emphasized];
    }

    if($marked instanceOf stdClass) {
      $marked = [$marked];
    }

    $content_header = $context->messages['reporting']['content_header'];
    $gutter_header = str_pad($context->messages['reporting']['gutter_header'], 5, ' ', STR_PAD_LEFT);

    $gutter_width = strlen($gutter_header) + 3;
    $columns_header = "  {$gutter_header} | {$content_header}\n";
    $omission = str_repeat(' ', $gutter_width - 5) . "...\n";

    $snippet = '';

    if(isset($context->source_label)) {
      $snippet .= "{$context->source_label}\n";
    }
    $snippet .= $columns_header;

    $in_omission = false;

    foreach($context->instructions as $instruction) {
      $emphasize = in_array($instruction, $emphasized);
      $mark = in_array($instruction, $marked);

      $show = false;
      foreach(array_merge($emphasized, $marked) as $marked_instruction) {
        if($instruction->line >= $marked_instruction->line - 2 &&
           $instruction->line <= $marked_instruction->line + 2) {
          $show = true;
          break;
        }
      }

      if($show) {
        $content = substr($context->input, $instruction->index, $instruction->length);
        $number = (string)($instruction->line + $context->indexing);

        if($emphasize) {
          $snippet .= " >" . str_pad($number, $gutter_width - 3, ' ', STR_PAD_LEFT) . " | {$content}\n";
        } else if($mark) {
          $snippet .= " *" . str_pad($number, $gutter_width - 3, ' ', STR_PAD_LEFT) . " | {$content}\n";
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
