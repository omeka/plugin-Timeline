<?php

/**
 * TimelineJS view timeline data helper.
 *
 */
class TimelineJS_View_Helper_GetTimelineData extends Zend_View_Helper_Abstract
{
    /**
     * Minimum and maximum years.
     *
     * When converted to Unix timestamps, anything outside this range would
     * exceed the minimum or maximum range for a 64-bit integer.
     */
    const YEAR_MIN = -292277022656;
    const YEAR_MAX = 292277026595;
    
    /**
     * ISO 8601 datetime pattern
     *
     * The standard permits the expansion of the year representation beyond
     * 0000–9999, but only by prior agreement between the sender and the
     * receiver. Given that our year range is unusually large we shouldn't
     * require senders to zero-pad to 12 digits for every year. Users would have
     * to a) have prior knowledge of this unusual requirement, and b) convert
     * all existing ISO strings to accommodate it. This is needlessly
     * inconvenient and would be incompatible with most other systems. Instead,
     * we require the standard's zero-padding to 4 digits, but stray from the
     * standard by accepting non-zero padded integers beyond -9999 and 9999.
     *
     * Note that we only accept ISO 8601's extended format: the date segment
     * must include hyphens as separators, and the time and offset segments must
     * include colons as separators. This follows the standard's best practices,
     * which notes that "The basic format should be avoided in plain text."
     */
    const PATTERN_ISO8601 = '^(?<date>(?<year>-?\d{4,})(-(?<month>\d{2}))?(-(?<day>\d{2}))?)(?<time>(T(?<hour>\d{2}))?(:(?<minute>\d{2}))?(:(?<second>\d{2}))?)(?<offset>((?<offset_hour>[+-]\d{2})?(:(?<offset_minute>\d{2}))?)|Z?)$';
    
    /**
     * @var array Cache of date/times
     */
    protected static $dateTimes = [];
    
    /**
     * Build data for a timeline.
     *
     * @param stdClass $items
     * @param array $timeline
     * @return array
     */
    public function getTimelineData($items, $timeline = array())
    {        
        // Save item metadata field selections to single array
        $slideProperties = array();
        $slideProperties['timestamp'] = metadata($timeline, 'item_date') ?: '';
        $slideProperties['interval'] = metadata($timeline, 'item_interval') ?: '';
        $slideProperties['title'] = metadata($timeline, 'item_title') ?: null;
        $slideProperties['description'] = metadata($timeline, 'item_description') ?: null;

        // Create timeline event for each attachment
        foreach  ($items as $item) {
            $event = $this->getTimelineEvent($item, $slideProperties);
            if ($event) {
                $events[] = $event;
            }
        }

        $timelineData = [
            'title' => null,
            'events' => isset($events) ? $events : null,
        ];

        // Set the timeline title and description.
        if (metadata($timeline, 'title') || metadata($timeline, 'description')) {
            $timelineData['title'] = [
                'text' => [
                    'headline' => metadata($timeline, 'title'),
                    'text' => metadata($timeline, 'description'),
                ],
            ];
        }

        return $timelineData;
    }
    
    /**
     * Get a timeline event.
     *
     * @see https://timeline.knightlab.com/docs/json-format.html#json-slide
     * @param ItemRepresentation $item
     * @param array $slideProperties
     * @return array
     */
    public function getTimelineEvent($item, $slideProperties)
    {
        $property = null;
        $property_name = null;
        $value = null;
        $slideValues = array();
        foreach ($slideProperties as $property_name => $property_id) {
            try {
                $property = get_db()->getTable('Element')->find($property_id);
            } catch (NotFoundException $e) {
                // Invalid property.
                continue;
            }
            $value = metadata($item, [$property->getElementSet()->name, $property->name]);
            if ($value) {
                $slideValues[$property_name] = $value;
            }
        }
        if (!$slideValues) {
            // This item has no matching values.
            return;
        }

        // Set the unique ID and "text" object.
        $slideTitle = isset($slideValues['title']) ? $slideValues['title'] : '';
        $slideDesc = isset($slideValues['description']) ? $slideValues['description'] : '';
        $itemLink = link_to_item($slideTitle, array(), 'show', $item);
        $event = [
            'unique_id' => (string) $item->id, // must cast to string
            'text' => [
                'headline' => $itemLink,
                'text' => $slideDesc,
            ],
        ];
        
        // Set the "media" object.
        $file = $item->getFile();
        if ($file) {
            $event['media'] = [
                'url' => $file->getProperty('uri'),
                'thumbnail' => $file->getProperty('thumbnail_uri'),
                'link' => $itemLink,
                'alt' => $file->getProperty('display_title'),
            ];
        }
        
        // Set the start and end "date" objects.
        if (isset($slideValues['timestamp'])) {
            $dateTime = $this->getDateTimeFromValue($slideValues['timestamp']);
            if (isset($dateTime)) {
                $event['start_date'] = [
                    'year' => $dateTime['year'],
                    'month' => $dateTime['month'],
                    'day' => $dateTime['day'],
                    'hour' => $dateTime['hour'],
                    'minute' => $dateTime['minute'],
                    'second' => $dateTime['second'],
                ];
            }
        } 
        
        // If both timestamp-field and interval-field are set
        // and item has both values, interval-field overrides
        if (isset($slideValues['interval'])) {
            list($intervalStart, $intervalEnd) = explode('/', $slideValues['interval']);
            $dateTimeStart = Timestamp::getDateTimeFromValue($intervalStart);
            if (isset($dateTimeStart)) {
                $event['start_date'] = [
                    'year' => $dateTimeStart['year'],
                    'month' => $dateTimeStart['month'],
                    'day' => $dateTimeStart['day'],
                    'hour' => $dateTimeStart['hour'],
                    'minute' => $dateTimeStart['minute'],
                    'second' => $dateTimeStart['second'],
                ];
            }
            $dateTimeEnd = $this->getDateTimeFromValue($intervalEnd, false);
            if (isset($dateTimeEnd)) {
                $event['end_date'] = [
                    'year' => $dateTimeEnd['year'],
                    'month' => $dateTimeEnd['month_normalized'],
                    'day' => $dateTimeEnd['day_normalized'],
                    'hour' => $dateTimeEnd['hour_normalized'],
                    'minute' => $dateTimeEnd['minute_normalized'],
                    'second' => $dateTimeEnd['second_normalized'],
                ];
            }
            $event['display_date'] = sprintf(
                '%s — %s',
                isset($dateTimeStart) ? $dateTimeStart['date']->format($dateTimeStart['format_render']) : '',
                isset($dateTimeEnd) ? $dateTimeEnd['date']->format($dateTimeEnd['format_render']) : ''
            );
        }

        return $event;
    }
    
    /**
     * Get relevant date/time information from an ISO 8601 value.
     *
     * Sets the decomposed date/time, format patterns, and the DateTime and
     * IntlCalendar objects to an array and returns the array.
     *
     * Use $defaultFirst to set the default of each datetime component to its
     * first (true) or last (false) possible integer, if the specific component
     * is not passed with the value.
     *
     * Also used to validate the datetime since validation is a side effect of
     * parsing the value into its component datetime pieces.
     *
     * @throws InvalidArgumentException
     * @param string $value An ISO 8601 string
     * @param bool $defaultFirst
     * @return array
     */
    public static function getDateTimeFromValue($value, $defaultFirst = true)
    {
        if (isset(self::$dateTimes[$value][$defaultFirst ? 'first' : 'last'])) {
            return self::$dateTimes[$value][$defaultFirst ? 'first' : 'last'];
        }

        // Match against ISO 8601, allowing for reduced accuracy.
        $isMatch = preg_match(sprintf('/%s/', self::PATTERN_ISO8601), $value, $matches);
        if (!$isMatch) {
            return;
        }
        $matches = array_filter($matches); // remove empty values
        // An hour requires a day.
        if (isset($matches['hour']) && !isset($matches['day'])) {
            return;
        }
        // An offset requires a time.
        if (isset($matches['offset']) && !isset($matches['time'])) {
            return;
        }

        // Set the datetime components included in the passed value.
        $dateTime = [
            'value' => $value,
            'date_value' => $matches['date'],
            'time_value' => $matches['time'] ?? null,
            'offset_value' => $matches['offset'] ?? null,
            'year' => (int) $matches['year'],
            'month' => isset($matches['month']) ? (int) $matches['month'] : null,
            'day' => isset($matches['day']) ? (int) $matches['day'] : null,
            'hour' => isset($matches['hour']) ? (int) $matches['hour'] : null,
            'minute' => isset($matches['minute']) ? (int) $matches['minute'] : null,
            'second' => isset($matches['second']) ? (int) $matches['second'] : null,
            'offset_hour' => isset($matches['offset_hour']) ? (int) $matches['offset_hour'] : null,
            'offset_minute' => isset($matches['offset_minute']) ? (int) $matches['offset_minute'] : null,
        ];

        // Set the normalized datetime components. Each component not included
        // in the passed value is given a default value.
        $dateTime['month_normalized'] = $dateTime['month'] ?? ($defaultFirst ? 1 : 12);
        // The last day takes special handling, as it depends on year/month.
        $dateTime['day_normalized'] = $dateTime['day']
            ?? ($defaultFirst ? 1 : self::getLastDay($dateTime['year'], $dateTime['month_normalized']));
        $dateTime['hour_normalized'] = $dateTime['hour'] ?? ($defaultFirst ? 0 : 23);
        $dateTime['minute_normalized'] = $dateTime['minute'] ?? ($defaultFirst ? 0 : 59);
        $dateTime['second_normalized'] = $dateTime['second'] ?? ($defaultFirst ? 0 : 59);
        $dateTime['offset_hour_normalized'] = $dateTime['offset_hour'] ?? 0;
        $dateTime['offset_minute_normalized'] = $dateTime['offset_minute'] ?? 0;
        // Set the UTC offset (+00:00) if no offset is provided.
        $dateTime['offset_normalized'] = isset($dateTime['offset_value'])
            ? ('Z' === $dateTime['offset_value'] ? '+00:00' : $dateTime['offset_value'])
            : '+00:00';

        // Validate ranges of the datetime component.
        if ((self::YEAR_MIN > $dateTime['year']) || (self::YEAR_MAX < $dateTime['year'])) {
            throw new InvalidArgumentException(sprintf('Invalid year: %s', $dateTime['year']));
        }
        if ((1 > $dateTime['month_normalized']) || (12 < $dateTime['month_normalized'])) {
            throw new InvalidArgumentException(sprintf('Invalid month: %s', $dateTime['month_normalized']));
        }
        if ((1 > $dateTime['day_normalized']) || (31 < $dateTime['day_normalized'])) {
            throw new InvalidArgumentException(sprintf('Invalid day: %s', $dateTime['day_normalized']));
        }
        if ((0 > $dateTime['hour_normalized']) || (23 < $dateTime['hour_normalized'])) {
            throw new InvalidArgumentException(sprintf('Invalid hour: %s', $dateTime['hour_normalized']));
        }
        if ((0 > $dateTime['minute_normalized']) || (59 < $dateTime['minute_normalized'])) {
            throw new InvalidArgumentException(sprintf('Invalid minute: %s', $dateTime['minute_normalized']));
        }
        if ((0 > $dateTime['second_normalized']) || (59 < $dateTime['second_normalized'])) {
            throw new InvalidArgumentException(sprintf('Invalid second: %s', $dateTime['second_normalized']));
        }
        if ((-23 > $dateTime['offset_hour_normalized']) || (23 < $dateTime['offset_hour_normalized'])) {
            throw new InvalidArgumentException(sprintf('Invalid hour offset: %s', $dateTime['offset_hour_normalized']));
        }
        if ((0 > $dateTime['offset_minute_normalized']) || (59 < $dateTime['offset_minute_normalized'])) {
            throw new InvalidArgumentException(sprintf('Invalid minute offset: %s', $dateTime['offset_minute_normalized']));
        }

        // Set the ISO 8601 format and render format.
        if (isset($dateTime['month']) && isset($dateTime['day']) && isset($dateTime['hour']) && isset($dateTime['minute']) && isset($dateTime['second']) && isset($dateTime['offset_value'])) {
            $formatIso8601 = 'Y-m-d\TH:i:sP';
            $formatRender = 'j F Y H:i:s P';
            $formatRenderIntl = 'd LLLL y G, HH:mm:ss xxx';
        } elseif (isset($dateTime['month']) && isset($dateTime['day']) && isset($dateTime['hour']) && isset($dateTime['minute']) && isset($dateTime['offset_value'])) {
            $formatIso8601 = 'Y-m-d\TH:iP';
            $formatRender = 'j F Y H:i P';
            $formatRenderIntl = 'd LLLL y G, HH:mm xxx';
        } elseif (isset($dateTime['month']) && isset($dateTime['day']) && isset($dateTime['hour']) && isset($dateTime['offset_value'])) {
            $formatIso8601 = 'Y-m-d\THP';
            $formatRender = 'j F Y H P';
            $formatRenderIntl = 'd LLLL y G, HH xxx';
        } elseif (isset($dateTime['month']) && isset($dateTime['day']) && isset($dateTime['hour']) && isset($dateTime['minute']) && isset($dateTime['second'])) {
            $formatIso8601 = 'Y-m-d\TH:i:s';
            $formatRender = 'j F Y H:i:s';
            $formatRenderIntl = 'd LLLL y G, HH:mm:ss';
        } elseif (isset($dateTime['month']) && isset($dateTime['day']) && isset($dateTime['hour']) && isset($dateTime['minute'])) {
            $formatIso8601 = 'Y-m-d\TH:i';
            $formatRender = 'j F Y H:i';
            $formatRenderIntl = 'd LLLL y G, HH:mm';
        } elseif (isset($dateTime['month']) && isset($dateTime['day']) && isset($dateTime['hour'])) {
            $formatIso8601 = 'Y-m-d\TH';
            $formatRender = 'j F Y H';
            $formatRenderIntl = 'd LLLL y G, HH:mm';
        } elseif (isset($dateTime['month']) && isset($dateTime['day'])) {
            $formatIso8601 = 'Y-m-d';
            $formatRender = 'j F Y';
            $formatRenderIntl = 'd LLLL y G';
        } elseif (isset($dateTime['month'])) {
            $formatIso8601 = 'Y-m';
            $formatRender = 'F Y';
            $formatRenderIntl = 'LLLL y G';
        } else {
            $formatIso8601 = 'Y';
            $formatRender = 'Y';
            $formatRenderIntl = 'y G';
        }
        $dateTime['format_iso8601'] = $formatIso8601;
        $dateTime['format_render'] = $formatRender;
        $dateTime['format_render_intl'] = $formatRenderIntl;

        // Set the DateTime object.
        $dateTime['date'] = new DateTime('now', new DateTimeZone($dateTime['offset_normalized']));
        $dateTime['date']->setDate(
            $dateTime['year'],
            $dateTime['month_normalized'],
            $dateTime['day_normalized']
        )->setTime(
            $dateTime['hour_normalized'],
            $dateTime['minute_normalized'],
            $dateTime['second_normalized']
        );

        self::$dateTimes[$value][$defaultFirst ? 'first' : 'last'] = $dateTime; // Cache the date/time
        return $dateTime;
    }
}
