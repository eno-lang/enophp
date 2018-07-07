<?php

require_once('src/tokenizer.php');

describe('Tokenizer', function() {
  describe('Sample', function() {
    it('tokenizes according to specification', function() {
      // $input = <<<DOC
      // > Comment
      //
      // language:
      // language: eno
      //
      // entry = value
      // - list item
      // | continue
      // \ continue
      // DOC;

      $input = file_get_contents('spec/sample.eno');

      $context = [
        'indexing' => 1,
        'input' => $input,
        'locale' => 'en'
      ];

      tokenize($context);

      $json = json_encode($context['instructions'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

      $snapshot = file_get_contents('spec/snapshots/tokenizer.snap.json');

      if($snapshot === false) {
        file_put_contents('spec/snapshots/tokenizer.snap.json', $json);
        $snapshot = $json;
      }

      expect($json)->toEqual($snapshot);
    });
  });
});

?>
