<?php
/**
 * The show view for the TimelineJS administrative panel.
 */

queue_timelinejs_assets();
$timelineTitle = metadata($timelinejs, 'title');
$head = array('bodyclass' => 'timelines primary',
              'title' => __('TimelineJS | %s', strip_formatting($timelineTitle))
              );
echo head($head);
?>

<div id="primary" class="seven columns alpha">

    <!-- Construct the timeline. -->
    <?php echo $this->partial('timelines/_timeline.php', array('items' => $items, 'timelinejs' => $timelinejs)); ?>

<?php
$query = isset($timelinejs->query) ? unserialize($timelinejs->query): [];
if ($query && is_array($query)) {
?>
        <h2><?php echo __('Items Query'); ?></h2>
        <p><strong><?php echo __('The &#8220;%s&#8221; timeline displays items that match the following query:', $timelineTitle); ?></strong></p>
        <?php
echo item_search_filters($query);
} ?>

</div>

<div class="three columns omega">
<div id="edit" class="panel">
<?php if (is_allowed($timelinejs, 'edit')): ?>
    <?php echo link_to($timelinejs, 'edit', __('Edit Metadata'), array('class' => 'big green button')); ?>
    <?php echo link_to($timelinejs, 'query', __('Edit Items Query'), array('class' => 'big green button')); ?>
<?php endif; ?>
<a href="<?php echo html_escape(public_url('timeline-js/timelines/show/'.timelinejs('id', null, $timelinejs))); ?>" class="big blue button"><?php echo __('View Public Page'); ?></a>
<?php echo link_to($timelinejs, 'delete-confirm', __('Delete'), array('class' => 'delete-confirm big red button')); ?>
</div>
</div>
<?php echo foot(); ?>
