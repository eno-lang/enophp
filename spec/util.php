<?php

// TODO: Either dynamic decision (e.g. extension) for serialization, or method differentiation (json_snapshot, snapshot)

function snapshot($file, $data) {
  $snapshot = @file_get_contents($file);

  if($snapshot === false) {
    // $snapshot = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($file, $data);

    return $data;
  } else {
    // return json_decode($snapshot);
    return $snapshot;
  }
}
