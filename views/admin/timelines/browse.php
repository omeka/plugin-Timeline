<?php
/**
 * The browse view for the Timeline administrative panel.
 */
queue_js_file('timeline-order');
$head = array('bodyclass' => 'timelines primary', 
              'title' => html_escape(__('Timeline | Browse Timelines')));
echo head($head);
?>
<?php echo flash(); ?>
<?php if ($timelines) : ?>
<?php echo pagination_links(); ?>
<?php if (is_allowed('Timeline_Timelines', 'add')): ?>
    <p><a href="<?php echo html_escape(url('timeline/timelines/add')); ?>" class="add full-width-mobile button green">
        <?php echo __('Add a Timeline'); ?>
    </a>
    <button id="reset-button" class="add full-width-mobile button"><?php echo __('Reset Timeline order'); ?></button>
    </p>
<?php endif; ?>
<p id="message" style="color: green;"></p>
<p>Drag and drop timelines in the order you would like them displayed on the <a href="<?php echo html_escape(public_url('timeline')); ?>" target="_blank">public timeline page</a>.</p>
<ul id="sortable" class="ui-sortable">
    <?php foreach ($timelines as $timeline): ?>
    <li id="timelines-<?php echo html_escape($timeline->id) ?>" class="ui-state-default sortable-item drawer"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
        <span class="move icon" aria-label="<?php echo __('Move'); ?>" title="<?php echo __('Move'); ?>"></span>
        <span class="timeline-title drawer-name"><?php echo link_to($timeline, 'show', $timeline->title); ?></span>
        <div class="other-meta">
                <?php
                    if (is_allowed($timeline, 'edit')) {
                        echo link_to($timeline, 'edit', __('Edit Metadata')) . ' | ';
                    }
                    if (is_allowed($timeline, 'query')) {
                        echo link_to($timeline, 'query', __('Edit Item Query')) . ' | ';
                    }
                    if (is_allowed($timeline, 'delete')) {
                        echo link_to($timeline, 'delete-confirm', __('Delete'), array('class' => 'delete-confirm'));
                    }
                ?>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
<?php echo pagination_links(); ?>

<?php else : ?>
    <p><?php echo __('There are no timelines.'); ?> <?php if (is_allowed('Timeline_Timelines', 'add')): ?><a href="<?php echo html_escape(url('timeline/timelines/add')); ?>"><?php echo __('Add a Timeline'); ?>.</a><?php endif; ?></p>
<?php endif; ?>
<?php echo foot(); ?>
