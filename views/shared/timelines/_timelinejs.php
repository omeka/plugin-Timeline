<?php
/**
 * Timeline display partial.
 */
$timelineData = json_encode($this->getTimelineData($this->items, $this->timelinejs));
?>

<!-- Container -->
<div id="<?php echo timeline_id($this->timelinejs); ?>" style="width: 100%; height: 500px" class="timeline-timeline" data-timeline-data="<?php echo html_escape($timelineData); ?>">
</div>
<script>
  jQuery(document).ready(function($) {
        var timelineDiv = jQuery('.timeline-timeline');
        var timelineData = timelineDiv.length ? timelineDiv.data('timeline-data') : null;
        if (timelineDiv.length) {
            var timelineOptions = {
                font: '<?php echo metadata($this->timelinejs, 'font'); ?>',
            }
        } else {
            var timelineOptions = null;
        }
        var timeline = timelineDiv.length ? new TL.Timeline(timelineDiv[0], timelineData, timelineOptions) : null;
    });
</script>
