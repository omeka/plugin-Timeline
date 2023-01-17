<?php
/**
 * The add view for the Timeline administrative panel.
 */

$head = array('bodyclass' => 'timelines primary', 
              'title' => html_escape(__('Timeline | Add a Timeline')));
echo head($head);
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
