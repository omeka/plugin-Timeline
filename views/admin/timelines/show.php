<?php
/**
 * The show view for the Timeline administrative panel.
 */

$timelineTitle = metadata($timeline, 'title');
$head = array('bodyclass' => 'timelines primary',
              'title' => __('Timeline | %s', strip_formatting($timelineTitle))
              );
echo head($head);
?>

<div id="primary" class="seven columns alpha">

    <!-- Construct the timeline. -->
    <?php echo $this->partial('timelines/_timelinejs.php', array('items' => $items, 'timelinejs' => $timeline)); ?>

<?php
$query = isset($timeline->query) ? unserialize($timeline->query): [];
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
<?php if (is_allowed($timeline, 'edit')): ?>
    <?php echo link_to($timeline, 'edit', __('Edit Metadata'), array('class' => 'big green button')); ?>
    <?php echo link_to($timeline, 'query', __('Edit Items Query'), array('class' => 'big green button')); ?>
<?php endif; ?>
<a href="<?php echo html_escape(public_url('timeline/show/'.timeline_field('id', null, $timeline))); ?>" class="big blue button"><?php echo __('View Public Page'); ?></a>
<?php echo link_to($timeline, 'delete-confirm', __('Delete'), array('class' => 'delete-confirm big red button')); ?>
</div>
</div>
<?php echo foot(); ?>
