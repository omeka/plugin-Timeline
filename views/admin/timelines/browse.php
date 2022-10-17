<?php
/**
 * The browse view for the TimelineJS administrative panel.
 */

$head = array('bodyclass' => 'timelines primary', 
              'title' => html_escape(__('TimelineJS | Browse Timelines')));
echo head($head);
?>
<?php echo flash(); ?>
<?php if ($total_results) : ?>
<?php echo pagination_links(); ?>
<?php if (is_allowed('TimelineJS_Timelines', 'add')): ?>
    <a href="<?php echo html_escape(url('timeline-js/timelines/add')); ?>" class="add full-width-mobile button green">
        <?php echo __('Add a Timeline'); ?>
    </a>
<?php endif; ?>
<table>
    <thead id="timelines-table-head">
        <tr>
        <th><?php echo __('Title'); ?></th>
        <th><?php echo __('Description'); ?></th>
        </tr>
    </thead>
    <tbody id="types-table-body">
        <?php foreach (loop('TimelineJS') as $timeline): ?>
        <tr>
            <td class="timeline-title title">
                <?php echo link_to($timeline, 'show', $timeline->title); ?>
                <ul class="action-links group">
                        <?php if (is_allowed($timeline, 'edit')): ?>
                        <li><?php echo link_to($timeline, 'edit', __('Edit Metadata')); ?></li>
                        <?php endif; ?>
                        <?php if (is_allowed($timeline, 'query')): ?>
                        <li><?php echo link_to($timeline, 'query', __('Edit Item Query')); ?></li>
                        <?php endif; ?>

                        <?php if (is_allowed($timeline, 'delete')): ?>
                        <li><?php echo link_to($timeline, 'delete-confirm', __('Delete'), array('class' => 'delete-confirm')); ?></li>
                        <?php endif; ?>
                </ul>
            </td>
            <td><?php echo snippet_by_word_count(metadata($timeline, 'description'), '10'); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php echo pagination_links(); ?>

<?php else : ?>
    <p><?php echo __('There are no timelines.'); ?> <?php if (is_allowed('TimelineJS_Timelines', 'add')): ?><a href="<?php echo html_escape(url('timeline-js/timelines/add')); ?>"><?php echo __('Add a Timeline'); ?>.</a><?php endif; ?></p>
<?php endif; ?>
<?php echo foot(); ?>
