<?php $timelineData = json_encode($this->getTimelineData($attachments, $options)); ?>
<div class='timeline-block'>
    <div class='timeline' style='height: 500px' data-timeline-data='<?php echo html_escape($timelineData); ?>'>
    </div>
</div>

<script type='text/javascript'>
jQuery(function() {
    var timelineDiv = jQuery('.timeline');
    var timelineData = timelineDiv.length ? timelineDiv.data('timeline-data') : null;
    var timeline = timelineDiv.length ? new TL.Timeline(timelineDiv[0], timelineData) : null;
});
</script>
