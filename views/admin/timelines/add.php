<?php
/**
 * The add view for the TimelineJS administrative panel.
 */

$head = array('bodyclass' => 'timelines primary', 
              'title' => html_escape(__('TimelineJS | Add a Timeline')));
echo head($head);

echo $form;

echo foot();
