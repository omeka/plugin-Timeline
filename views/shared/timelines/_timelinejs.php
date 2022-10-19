<?php
/**
 * Timeline display partial.
 */
$timelineData = json_encode($this->getTimelineData($this->items, $this->timelinejs));
?>

<!-- Container. -->
<div id="<?php echo timelinejs_id($this->timelinejs); ?>" style="width: 100%; height: 500px" class="timelinejs-timeline" data-timeline-data="<?php echo html_escape($timelineData); ?>">
</div>
<script>
  jQuery(document).ready(function($) {
        var timelineDiv = jQuery('.timelinejs-timeline');
        var timelineData = timelineDiv.length ? timelineDiv.data('timeline-data') : null;
        var timeline = timelineDiv.length ? new TL.Timeline(timelineDiv[0], timelineData, null) : null;
    });
</script>
