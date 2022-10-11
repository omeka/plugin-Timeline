<?php
/**
 * The edit view for the TimelineJS administrative panel.
 */

$timelineTitle = timelinejs('title') ? strip_formatting(timelinejs('title')) : '[Untitled]';
$title = __('TimelineJS | Edit "%s" Metadata', $timelineTitle);
$head = array('bodyclass' => 'timelines primary',
              'title' => html_escape($title));
echo head($head);
echo flash();
echo $form;

echo foot();
