<?php

use Eno\Reporters\Terminal;

describe('Terminal reporter', function() {
  given('_context', function() {
    require('context_fixture.php');
    return $context;
  });

  it('produces text output', function() {
    $this->_context['reporter'] = 'terminal';

    $snippet = Terminal::report(
      $this->_context,
      $this->_context['instructions'][1],
      $this->_context['instructions'][0]
    );

    expect($snippet)->toEqual(snapshot('spec/reporters/snapshots/terminal.snap.sh', $snippet));
  });
});