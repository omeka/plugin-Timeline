<?php
/**
 * The public show view for Timelines.
 */

queue_timeline_assets();
$head = array('bodyclass' => 'timelines primary',
              'title' => metadata($timelinejs, 'title')
              );
echo head($head);
?>
<h1><?php echo metadata($timelinejs, 'title'); ?></h1>

    <!-- Construct the timeline. -->
    <?php echo $this->partial('timelines/_timeline.php', array('center_date' => metadata($timelinejs, 'center_date'))); ?>

    <?php echo metadata($timelinejs, 'description'); ?>

<?php echo foot(); ?>
