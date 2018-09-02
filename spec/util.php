<?php

// TODO: Either dynamic decision (e.g. extension) for serialization, or method differentiation (json_snapshot, snapshot)
// TODO: Consider outputting a "xxx.new_diff" file if the snapshot does not match the new comparison value to be able
//       to do a quick diff with meld or similar (to augment the weak built-in diff from kahlan's CLI reporter)

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
