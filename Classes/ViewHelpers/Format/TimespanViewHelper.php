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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
USE BrainAppeal\CampusEventsConnector\Domain\Model\TimeRange;

/**
 * ViewHelper to render the time span information
 *
 * @package campus_event
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TimespanViewHelper extends AbstractViewHelper {


    /**
     * Render the supplied DateTime object as a formatted date.
     *
     * @param TimeRange $timeRange A TIME RANGE INSTACE
     * @param string $format Format for start date (without time part!)
     * @param bool $showDate
     * @param bool $showTime
     * @return string Formatted date time span
     * @throws \Exception
     */
    public function render(TimeRange $timeRange, $format = null, $showDate = true, $showTime = true) {

        $start = $this->getDateTimeObj($timeRange->getStartDate());
        $end = $this->getDateTimeObj($timeRange->getEndDate());
        if (empty($format)) {
            $format = '%A, %d. %B %Y';
        }
        $startDay = $this->format($start, $format);
        $endDay = $this->format($end, $format);
        $formattedTimeRange = '';
        if ($showTime) {
            $startTime = $this->format($start, '%H:%M');
            $endTime = $this->format($end, '%H:%M');
            if ($showDate) {
                if ($endDay != $startDay) {
                    $endTime = $endDay . ', ' . $endTime;
                }
                $timeAppend = $endTime != $startTime ? ' - ' . $endTime : '';
                $formattedTimeRange = $startDay . ', ' . $startTime . $timeAppend;
            } else {
                $formattedTimeRange = $startTime != $endTime ? $startTime . ' &ndash; ' . $endTime : $startTime;
            }
        } elseif ($showDate) {
            $formattedTimeRange = $startDay != $endDay ? $startDay . ' &ndash; ' . $endDay : $startDay;
        }

        return $formattedTimeRange;
    }

    private function format(\DateTime $date, $format)
    {
        if (strpos($format, '%') !== FALSE) {
            return strftime($format, $date->format('U'));
        } else {
            return $date->format($format);
        }
    }

    /**
     * Create a datetime object from the given parameter (that may be a string or already a DateTime object)
     *
     * @param string|\DateTime $date
     * @return \DateTime
     * @throws \Exception
     */
    private function getDateTimeObj($date)
    {
        if (!$date instanceof \DateTime) {
            try {
                if (is_integer($date)) {
                    $date = new \DateTime('@' . $date);
                } else {
                    $date = new \DateTime($date);
                }
                $date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            } catch (\Exception $exception) {
                throw new \Exception('"' . $date . '" could not be parsed by DateTime constructor.', 1241722579);
            }
        }
        return $date;
    }
}
