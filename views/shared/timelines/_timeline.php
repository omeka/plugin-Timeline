<?php
/**
 * Timeline display partial.
 */
?>

<!-- Container. -->
<div id="<?php echo timelinejs_timeline_id(); ?>" class="timelinejs-timeline">
</div>
<script>
  jQuery(document).ready(function($) {
        var centerDate = '<?php echo $this->center_date; ?>';

        TimelineJS.loadTimeline(
            '<?php echo timelinejs_timeline_id(); ?>',
            '<?php echo timelinejs_json_uri_for_timeline(); ?>'
        );
    });
</script>
