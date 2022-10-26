<?php
  if (!array_key_exists('timeline-id', $options)) {
      return;
  }
  $timeline = get_record_by_id('Timeline',  $options['timeline-id']);
  if (!$timeline) {
      return;
  }

  if (isset($timeline->query)) {
      $items = get_db()->getTable('Item')->findBy(unserialize($timeline->query), null);
  } else {
      $items = [];
  }

  echo $this->partial('timelines/_timelinejs.php', array('items' => $items, 'timelinejs' => $timeline));
?>
