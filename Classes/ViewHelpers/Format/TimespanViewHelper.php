<?php
/**
 * campus_events_frontend comes with ABSOLUTELY NO WARRANTY
 * See the GNU GeneralPublic License for more details.
 * https://www.gnu.org/licenses/gpl-2.0
 *
 * Copyright (C) 2019 Brain Appeal GmbH
 *
 * @copyright 2019 Brain Appeal GmbH (www.brain-appeal.com)
 * @license   GPL-2 (www.gnu.org/licenses/gpl-2.0)
 * @link      https://www.campus-events.com/
 */

namespace BrainAppeal\CampusEventsFrontend\ViewHelpers\Format;

use DateTime;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use BrainAppeal\CampusEventsConnector\Domain\Model\TimeRange;

/**
 * Format a given time span with IntlDateFormatter
 *
 * @package campus_event
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TimespanViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('timeRange', TimeRange::class, 'A time range model instance', true);
        $this->registerArgument('format', 'string', 'The desired date format pattern (IntlDateFormatter)', false, 'dd.MM.YYYY');
        $this->registerArgument('showDate', 'bool', 'Toggle display of date', false, true);
        $this->registerArgument('showTime', 'bool', 'Toggle display of time', false, true);
    }

    /**
     * Render the supplied DateTime object as a formatted date.
     *
     * @return string Formatted date time span
     * @throws \Exception
     */
    public function render()
    {
        $timeRange = $this->arguments['timeRange'];
        $format = $this->arguments['format'];
        $showDate = $this->arguments['showDate'];
        $showTime = $this->arguments['showTime'];

        $start = $this->getDateTimeObj($timeRange->getStartDate());
        $end = $this->getDateTimeObj($timeRange->getEndDate());
        if ($format === '%a, %d.%m.%Y' || $format === 'shortDayAndDate') {
            $format = 'EEE, dd.MM.YYYY';
        } elseif ($format === '%A, %d.%m.%Y' || $format === 'longDayAndDate') {
            $format = 'EEEE, dd.MM.YYYY';
        } elseif ($format === 'd.m.Y') {
            $format = 'dd.MM.YYYY';
        }
        $startDay = $this->format($start, $format);
        $endDay = $this->format($end, $format);
        $formattedTimeRange = '';
        if ($showTime) {
            $startTime = $this->format($start, 'HH:mm');
            $endTime = $this->format($end, 'HH:mm');
            if ($showDate) {
                if ($endDay !== $startDay) {
                    $endTime = $endDay . ', ' . $endTime;
                }
                $timeAppend = $endTime !== $startTime ? ' - ' . $endTime : '';
                $formattedTimeRange = $startDay . ', ' . $startTime . $timeAppend;
            } else {
                $formattedTimeRange = $startTime !== $endTime ? $startTime . ' &ndash; ' . $endTime : $startTime;
            }
        } elseif ($showDate) {
            $formattedTimeRange = $startDay !== $endDay ? $startDay . ' &ndash; ' . $endDay : $startDay;
        }

        return $formattedTimeRange;
    }

    /**
     * Format the given date
     * @param DateTime $date The date and time object
     * @param string $pattern Pattern for IntlDateFormatter
     * @return bool|string
     */
    private function format(DateTime $date, string $pattern)
    {
        $fmt = new \IntlDateFormatter(
            'de-DE',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            $this->getTimezoneString(),
            \IntlDateFormatter::GREGORIAN,
            $pattern
        );
        return $fmt->format($date);
    }

    /**
     * Create a datetime object from the given parameter (that may be a string or already a DateTime object)
     *
     * @param string|DateTime $date
     * @return DateTime
     * @throws \Exception
     */
    private function getDateTimeObj($date): DateTime
    {
        if (!$date instanceof DateTime) {
            try {
                if (is_numeric($date)) {
                    $date = new DateTime('@' . $date);
                } else {
                    $date = new DateTime($date);
                }
                $date->setTimezone(new \DateTimeZone($this->getTimezoneString()));
            } catch (\Exception $e) {
                throw new \Exception(sprintf('"' . $date . '" could not be parsed by DateTime constructor: %s.', $e->getMessage()), 1241722579);
            }
        }
        return $date;
    }

    /**
     * Return the current time zone string
     * @return string
     */
    private function getTimezoneString(): string
    {
        $timeZone = date_default_timezone_get();
        if (empty($timeZone) || strtolower($timeZone) === 'utc') {
            $timeZone = 'Europe/Berlin';
        }
        return $timeZone;
    }
}
