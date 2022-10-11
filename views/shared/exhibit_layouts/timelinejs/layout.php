<?php
  if (!array_key_exists('timeline-id', $options)) {
      return;
  }
  $timelinejs = get_record_by_id('TimelineJS',  $options['timeline-id']);
  if (!$timelinejs) {
      return;
  }
  set_current_record('timelinejs', $timelinejs);
  echo $this->partial('timelines/_timeline.php', array('items' => $items, 'timelinejs' => $timelinejs)));
?>
