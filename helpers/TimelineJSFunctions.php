<?php
/**
 * TimelineJS helper functions
 */

/**
 * Return specific field for a timelineJS record.
 *
 * @param string
 * @param array $options
 * @param TimelineJSTimeline|null
 * @return string
 */
function timelinejs($fieldname, $options = array(), $timeline = null)
{

    $timeline = $timeline ? $timeline : get_current_record('TimelineJS');

    return metadata($timeline, $fieldname, $options);

}

/**
 * Returns a link to a specific timeline.
 *
 * @param string HTML for the text of the link.
 * @param array Attributes for the <a> tag. (optional)
 * @param string The action for the link. Default is 'show'.
 * @param TimelineJSTimeline|null
 * @return string HTML
 **/
function link_to_timelinejs($text = null, $props = array(), $action = 'show', $timeline = null)
{

    $timeline = $timeline ? $timeline : get_current_record('TimelineJS');

    $text = $text ? $text : $timeline->title;

    return link_to($timeline, $action, $text, $props);

}

/**
 * Construct id for container div.
 *
 * @since 1.0
 * @param TimelineJSTimeline|null
 * @return string HTML
 */
function timelinejs_id($timeline = null)
{
    $timeline = $timeline ? $timeline : get_current_record('TimelineJS');
    return text_to_id(html_escape($timeline->title) . ' ' . $timeline->id, 'timelinejs');
}

/**
 * Shortcode for displaying Timelines.
 *
 * @param array $args
 * @param Omeka_View $view
 * @return string
 */
function timelinejs_shortcode($args, $view)
{
    if (!array_key_exists('title', $args)) {
        return;
    }
    $timelinejs = get_record('TimelineJS',  array('title'=>$args['title']));
    if (!$timelinejs) {
        return;
    }

    if (isset($timelinejs->query)) {
        $items = get_db()->getTable('Item')->findBy(unserialize($timelinejs->query), null);
    } else {
        $items = [];
    }

    return $view->partial('timelines/_timelinejs.php', array('items' => $items, 'timelinejs' => $timelinejs));
}
