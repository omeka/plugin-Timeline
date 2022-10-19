<?php
  if (!array_key_exists('timeline-id', $options)) {
      return;
  }
  $timelinejs = get_record_by_id('TimelineJS',  $options['timeline-id']);
  if (!$timelinejs) {
      return;
  }

  if (isset($timelinejs->query)) {
      $items = get_db()->getTable('Item')->findBy(unserialize($timelinejs->query), null);
  } else {
      $items = [];
  }

  echo $this->partial('timelines/_timelinejs.php', array('items' => $items, 'timelinejs' => $timelinejs));
?>
