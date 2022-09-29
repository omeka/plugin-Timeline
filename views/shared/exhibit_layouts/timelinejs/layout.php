<?php
  if (!array_key_exists('timeline-id', $options)) {
      return;
  }
  $timeline = get_record_by_id('TimelineJS',  $options['timeline-id']);
  if (!$timeline) {
      return;
  }
  set_current_record('timelinejs', $timeline);
  echo $this->partial('timelines/_timeline.php', array('center_date' => metadata($timeline, 'center_date')));
?>
