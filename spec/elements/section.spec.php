<?php

use Eno\{Section};

describe('Section', function() {
  beforeAll(function() {
    $this->_context = (object) [];
    $this->instruction = (object) [
      'depth' => 0,
      'index' => 0,
      'length' => 0,
      'line' => 1,
      'name' => '<>#:=|\\_ENO_DOCUMENT',
      'ranges' => [
        'section_operator' => [0, 0],
        'name' => [0, 0]
      ],
      'subinstructions' => []
    ];
  });

  beforeEach(function() {
    $this->section = new Section($this->_context, $this->instruction);
  });

  describe('elements()', function() {
    beforeEach(function() {
      $this->result = $this->section->elements();
    });

    it('touches the section', function() {
      expect($this->section->touched)->toBe(true);
    });

    it('returns the elements of the section', function() {
      expect($this->result)->toEqual([]);
    });
  });

  describe('__toString()', function() {
    it('returns a debug abstraction', function() {
      expect((string)$this->section)->toEqual('[Section document elements=0]');
    });
  });
});
