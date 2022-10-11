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
 * Queues JavaScript and CSS for TimelineJS in the page header.
 */
function queue_timelinejs_assets()
{
    $headScript = get_view()->headScript();

    queue_css_url('https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css');
    queue_js_url('https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js');

    queue_css_file('timelinejs');
}

// /**
//  * Returns the URI for a timeline's json output.
//  *
//  * @param TimelineJSTimeline|null
//  * @return string URL the items output uri for the timeline-json output.
//  */
// function timelinejs_json_uri_for_timeline($timeline = null)
// {
//     $timeline = $timeline ? $timeline : get_current_record('TimelineJS');
//     return record_url($timeline, 'items') . '?output=timelinejs-json';
// }

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

// /**
//  * Returns a string for timelinejs_json 'classname' attribute for an item.
//  *
//  * Default fields included are: 'item', item type name, all DC:Type values.
//  *
//  * Output can be filtered using the 'timelinejs_item_class' filter.
//  *
//  * @return string
//  */
// function timelinejs_item_class($item = null) {
//     $classArray = array('item');
// 
//     if ($itemTypeName = metadata($item, 'item_type_name')) {
//         $classArray[] = text_to_id($itemTypeName);
//     }
// 
//     if ($dcTypes = metadata($item, array('Dublin Core', 'Type'), array('all' => true))) {
//         foreach ($dcTypes as $type) {
//             $classArray[] = text_to_id($type);
//         }
//     }
// 
//     $classAttribute = implode(' ', $classArray);
//     $classAttribute = apply_filters('timelinejs_item_class', $classAttribute);
//     return $classAttribute;
// }

// /**
//  * Generates a form select populated by all elements and element sets.
//  *
//  * @param string The TimelineJS option name.
//  * @return string HTML.
//  */
// function timelinejs_option_select($name = null) {
// 
//   if ($name) {
//     return get_view()->formSelect(
//                     $name,
//                     timelinejs_get_option($name),
//                     array(),
//                     get_table_options('Element', null, array(
//                         'record_types' => array('Item', 'All'),
//                         'sort' => 'alphaBySet')
//                     )
//                 );
// 
//   }
// 
//     return false;
// 
// }

// /**
//  * Gets the value for an option set in the TimelineJS option array.
//  *
//  * @param string The TimelineJS option name.
//  * @return string
//  */
// function timelinejs_get_option($name = null) {
// 
//   if ($name) {
//     $options = get_option('timelinejs');
//     $options = unserialize($options);
//     return $options[$name];
//   }
// 
//   return false;
// 
// }

// /**
//  * Returns the value of an element set in the TimelineJS config options.
//  *
//  * @param string The TimelineJS option name.
//  * @param array An array of options.
//  * @param Item
//  * @return string|array|null
//  */
// function timelinejs_get_item_text($optionName, $options = array(), $item = null) {
// 
//     $element = get_db()->getTable('Element')->find(timelinejs_get_option($optionName));
// 
//     return metadata($item, array($element->getElementSet()->name, $element->name), $options);
// 
// }
