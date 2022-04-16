<?php declare(strict_types=1);

use Eno\{Errors\ParseError, Parser};

describe('Parser', function() {
  describe('parse()', function() {
    it('works in a basic scenario', function() {
      $document = Parser::parse('language: eno');
      expect($document->field('language'))->toEqual('eno');
    });

    it('does not accept non-string input', function() {
      $closure = function() {
        Parser::parse(42);
      };

      expect($closure)->toThrow(new TypeError());
    });

    it('does not accept unavailable locales', function() {
      $closure = function() {
        Parser::parse('language: eno', [ 'locale' => 'invalid' ]);
      };

      expect($closure)->toThrow(new OutOfRangeException());
    });
  });
});
