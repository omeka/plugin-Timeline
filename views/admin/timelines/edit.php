<?php
/**
 * The edit view for the Timeline administrative panel.
 */

$timelineTitle = metadata($timeline, 'title') ? metadata($timeline, 'title') : '[Untitled]';
$title = __('Timeline | Edit "%s" Metadata', $timelineTitle);
$head = array('bodyclass' => 'timelines primary',
              'title' => html_escape($title));
echo head($head);
echo flash();
?>

<form id="timeline-form" method="post">
<div id="primary" class="seven columns alpha">
<?php echo $form->getDisplayGroup('timeline_info'); ?>
</div>

<?php echo $csrf; ?>
<div class="three columns omega">
<div id="edit" class="panel">
<input type="submit" name="submit" id="submit" value="Save Timeline" class="big green button">
</div>
</div>
</form>

<?php echo foot(); ?>
