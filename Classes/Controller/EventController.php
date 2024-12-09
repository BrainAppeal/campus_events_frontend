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

namespace BrainAppeal\CampusEventsFrontend\Controller;

use BrainAppeal\CampusEventsConnector\Domain\Model\Event;
use TYPO3\CMS\Core\Cache\CacheTag;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * EventController
 */
class EventController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * event repository
     *
     * @var \BrainAppeal\CampusEventsConnector\Domain\Repository\EventRepository
     */
    protected $eventRepository;

    /**
     * Inject a event repository to enable DI
     *
     * @param \BrainAppeal\CampusEventsConnector\Domain\Repository\EventRepository $eventRepository
     */
    public function injectEventRepository(\BrainAppeal\CampusEventsConnector\Domain\Repository\EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * We only want to set the tag once in one request, so we have to cache that statically if it has been done
     *
     * @var bool
     */
    private static $cacheTagsSet = false;

    /**
     * Initializes the current action
     */
    public function initializeAction(): void
    {
        if (!self::$cacheTagsSet) {
            $cacheDataCollector = $this->request->getAttribute('frontend.cache.collector');
            // TYPO3 >= 13
            if ($cacheDataCollector) {
                $cacheDataCollector->addCacheTags(...array_map(fn(string $tag) => new CacheTag($tag), ['tx_campus_events']));
                self::$cacheTagsSet = true;
            // TYPO3 12
            } elseif (($GLOBALS['TSFE']??null) instanceof TypoScriptFrontendController) {
                /** @var TypoScriptFrontendController $typoScriptFrontendController  */
                $typoScriptFrontendController = $GLOBALS['TSFE'];
                $typoScriptFrontendController->addCacheTags(['tx_campus_events']);
                self::$cacheTagsSet = true;
            }
        }
    }

    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {
        /** @var ContentObjectRenderer $cObj */
        $cObj = $this->request->getAttribute('currentContentObject');
        $pidList = $this->settings['startingpoint'];
        $limit = (int)$this->settings['limit'];
        $timespan = $this->settings['timespan'] ?? '';
        $excludeFilterCategories = [];
        if (isset($this->settings['excludeFilterCategories'])) {
            $excludeFilterCategories = GeneralUtility::intExplode(',', $this->settings['excludeFilterCategories'], true);
        }
        $constraints = [];
        if (!empty($excludeFilterCategories)) {
            $query = $this->eventRepository->createQuery();
            $constraints[] = $query->logicalNot($query->contains('filterCategories', $excludeFilterCategories));
        }
        $events = $this->eventRepository->findListByPid($pidList, $constraints);
        if ($timespan !== 'all') {
            $events = $this->filterListAfterTimespan($events, $timespan);
        }
        if ($limit > 0 && count($events) > $limit) {
            $events = array_slice($events, 0, $limit);
        }
        $assignedValues = [
            'events' => $events,
            'contentData' => $cObj->data,
            'settings' => $this->settings,
        ];
        $this->view->assignMultiple($assignedValues);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(\BrainAppeal\CampusEventsConnector\Domain\Model\Event $event): \Psr\Http\Message\ResponseInterface
    {
        $assignedValues = [
            'event' => $event,
            'settings' => $this->settings,
        ];
        $this->view->assignMultiple($assignedValues);
        $GLOBALS['TSFE']->addCacheTags(['tx_campus_events_' . $event->getUid()]);
        return $this->htmlResponse();
    }

    /**
     * @param QueryResultInterface|Event[] $events
     * @param string $timespan
     * @return Event[]
     */
    private function filterListAfterTimespan($events, string $timespan): array
    {
        $timespan = (empty($timespan)) ? 'future' : $timespan;
        $currentDate = new \DateTime();
        $filteredEvents = [];
        $collectedEvents = [];

        $sort = 'ASC';
        foreach ($events as $event) {
            if (in_array($event->getUid(), $collectedEvents, false)) {
                continue;
            }
            $collectedEvents[] = $event->getUid();

            $startDate = $event->getStartDate();
            switch ($timespan) {
                case 'past':
                    $sort = 'DESC';
                    if ($startDate <= $currentDate) {
                        $filteredEvents[] = $event;
                    }
                    break;
                case 'future':
                    if ($startDate >= $currentDate) {
                        $filteredEvents[] = $event;
                    }
                    break;
            }
        }

        return $this->sortEvents($filteredEvents, $sort);
    }

    private function sortEvents($filteredEvents, $sort)
    {
        usort($filteredEvents, static function ($eventA, $eventB) use ($sort) {
            /** @var Event $eventA */
            /** @var Event $eventB */
            if (strtolower((string) $sort) === 'desc') {
                return $eventB->getStartDate() <=> $eventA->getStartDate();
            }

            return $eventA->getStartDate() <=> $eventB->getStartDate();
        });
        return $filteredEvents;
    }

    protected function getErrorFlashMessage(): bool|string
    {
        return false;
    }
}
