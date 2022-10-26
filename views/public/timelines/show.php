<?php
/**
 * The public show view for Timelines.
 */

$head = array('bodyclass' => 'timelines primary',
              'title' => metadata($timeline, 'title')
              );
echo head($head);
?>
<h1><?php echo metadata($timeline, 'title'); ?></h1>

    <!-- Construct the timeline. -->
    <?php echo $this->partial('timelines/_timelinejs.php', array('items' => $items, 'timelinejs' => $timeline)); ?>

    <?php echo metadata($timeline, 'description'); ?>

<?php echo foot(); ?>
