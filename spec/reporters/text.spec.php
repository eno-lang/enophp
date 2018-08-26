<?php

use Eno\Reporters\Text;

describe('Text reporter', function() {

  given('_context', function() {
    require_once('context_fixture.php');

    return $context;
  });

  it('produces text output', function() {
    $this->_context['reporter'] = 'html';

    $snippet = Text::report(
      $this->_context,
      $this->_context['instructions'][1],
      $this->_context['instructions'][0]
    );

    expect($snippet)->toEqual(snapshot('spec/reporters/snapshots/text.snap.json', $snippet));
  });
});
