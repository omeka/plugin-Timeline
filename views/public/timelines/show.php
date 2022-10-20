<?php
/**
 * The public show view for Timelines.
 */

$head = array('bodyclass' => 'timelines primary',
              'title' => metadata($timelinejs, 'title')
              );
echo head($head);
?>
<h1><?php echo metadata($timelinejs, 'title'); ?></h1>

    <!-- Construct the timeline. -->
    <?php echo $this->partial('timelines/_timelinejs.php', array('items' => $items, 'timelinejs' => $timelinejs)); ?>

    <?php echo metadata($timelinejs, 'description'); ?>

<?php echo foot(); ?>
