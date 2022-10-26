<?php
/**
 * Timeline helper functions
 */

/**
 * Return specific field for a timeline record.
 *
 * @param string
 * @param array $options
 * @param TimelineTimeline|null
 * @return string
 */
function timeline_field($fieldname, $options = array(), $timeline = null)
{

    $timeline = $timeline ? $timeline : get_current_record('Timeline');

    return metadata($timeline, $fieldname, $options);

}

/**
 * Returns a link to a specific timeline.
 *
 * @param string HTML for the text of the link.
 * @param array Attributes for the <a> tag. (optional)
 * @param string The action for the link. Default is 'show'.
 * @param TimelineTimeline|null
 * @return string HTML
 **/
function link_to_timelinejs($text = null, $props = array(), $action = 'show', $timeline = null)
{

    $timeline = $timeline ? $timeline : get_current_record('Timeline');

    $text = $text ? $text : $timeline->title;

    return link_to($timeline, $action, $text, $props);

}

/**
 * Construct id for container div.
 *
 * @since 1.0
 * @param TimelineTimeline|null
 * @return string HTML
 */
function timeline_id($timeline = null)
{
    $timeline = $timeline ? $timeline : get_current_record('Timeline');
    return text_to_id(html_escape($timeline->title) . ' ' . $timeline->id, 'timeline');
}

/**
 * Shortcode for displaying Timelines.
 *
 * @param array $args
 * @param Omeka_View $view
 * @return string
 */
function timeline_shortcode($args, $view)
{
    if (!array_key_exists('title', $args)) {
        return;
    }
    $timeline = get_record('Timeline', array('title'=>$args['title']));
    if (!$timeline) {
        return;
    }

    if (isset($timeline->query)) {
        $items = get_db()->getTable('Item')->findBy(unserialize($timeline->query), null);
    } else {
        $items = [];
    }

    return $view->partial('timelines/_timelinejs.php', array('items' => $items, 'timelinejs' => $timeline));
}
