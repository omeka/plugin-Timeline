<?php
/**
 * The shared timelinejs-json browse view for Items
 */

$timelinejsEvents = array();
foreach ($items as $item) {
    $itemTitle = strip_formatting(timelinejs_get_item_text('item_title', array(), $item));
    $itemLink = record_url($item);
    $itemDescription =  timelinejs_get_item_text('item_description', array('snippet' => '200'), $item);

    $itemDates = timelinejs_get_item_text('item_date', array('all' => true), $item);

    $fileUrl = null;
    if ($file = get_db()->getTable('File')->findWithImages(metadata($item, 'id'), 0)) {
        $fileUrl = metadata($file, 'square_thumbnail_uri');
    }

    if (!empty($itemDates)) {
      foreach ($itemDates as $itemDate) {
            $itemDate = $itemDate;

            $timelinejsEvent = array();
            $dateArray = explode('/', $itemDate);

            if ($dateStart = timelinejs_convert_date(trim($dateArray[0]))) {
                $timelinejsEvent['start'] = $dateStart;

                if (count($dateArray) == 2) {
                    $timelinejsEvent['end'] = timelinejs_convert_date(trim($dateArray[1]));
                }

                $timelinejsEvent['title'] = $itemTitle;
                $timelinejsEvent['link'] = $itemLink;
                $timelinejsEvent['classname'] = timelinejs_item_class($item);

                if ($fileUrl) {
                    $timelinejsEvent['image'] = $fileUrl;
                }

                $timelinejsEvent['description'] = $itemDescription;
                $timelinejsEvents[] = $timelinejsEvent;
            }
        }
    }
}

$timelinejsArray = array();
$timelinejsArray['dateTimeFormat'] = "iso8601";
$timelinejsArray['events'] = $timelinejsEvents;

$timelinejsJson = json_encode($timelinejsArray);

echo $timelinejsJson;
