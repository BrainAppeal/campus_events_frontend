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
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;

/**
 * IndexController
 */
class EventController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * event repository
     *
     * @var \BrainAppeal\CampusEventsConnector\Domain\Repository\EventRepository
     */
    protected $eventRepository = null;

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
     * Initializes the current action
     *
     * @return void
     */
    public function initializeAction()
    {
        // Only do this in Frontend Context
        if (!empty($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE'])) {
            // We only want to set the tag once in one request, so we have to cache that statically if it has been done
            static $cacheTagsSet = false;

            /** @var $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController */
            $typoScriptFrontendController = $GLOBALS['TSFE'];
            if (!$cacheTagsSet) {
                $typoScriptFrontendController->addCacheTags(['tx_campus_events']);
                $cacheTagsSet = true;
            }
        }
    }

    /**
     * action list
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function listAction()
    {
        $cObj = $this->configurationManager->getContentObject();
        $pidList = $this->settings['startingpoint'];
        $limit = (int) $this->settings['limit'];
        $timespan = $this->settings['timespan'];
        $events = $this->eventRepository->findListByPid($pidList);
        if ($timespan !== 'all') {
            $events = $this->filterListAfterTimespan($events,$timespan);
            $events = array_slice($events,0,$limit);
        }
        $assignedValues = [
            'events' => $events,
            'contentData' => $cObj->data,
            'settings' => $this->settings,
        ];
        $this->view->assignMultiple($assignedValues);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws \Exception
     */
    public function processRequest(RequestInterface $request, ResponseInterface $response)
    {
        try {
            parent::processRequest($request, $response);
        } catch (\Exception $exception) {
            $this->handleKnownExceptionsElseThrowAgain($exception);
        }
    }

    /**
     * @param \Exception $exception
     * @throws \Exception
     */
    private function handleKnownExceptionsElseThrowAgain(\Exception $exception)
    {
        $previousException = $exception->getPrevious();
        if (
            $this->actionMethodName === 'showAction'
            && $previousException instanceof \TYPO3\CMS\Extbase\Property\Exception
        ) {
            $this->redirect('list');
        } else {
            throw $exception;
        }
    }

    /**
     * action show
     *
     *
     * @return void
     */
    public function showAction(\BrainAppeal\CampusEventsConnector\Domain\Model\Event $event) {
        $assignedValues = [
            'event' => $event,
            'settings' => $this->settings,
        ];
        $this->view->assignMultiple($assignedValues);
        $GLOBALS['TSFE']->addCacheTags(['tx_campus_events_' . $event->getUid()]);
    }

    /**
     * @param Event[] $events
     * @param string $timespan
     * @return Event[]
     */
    private function filterListAfterTimespan($events, $timespan) {
        $timespan = (empty($timespan)) ? 'future' : $timespan;
        $currentDate = new \DateTime();
        $filteredEvents = [];
        $collectedEvents = [];

        $sort = 'ASC';
        foreach ($events as $eventKey => $event) {
            if (in_array($event->getUid(), $collectedEvents)) {
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

        $filteredEvents = $this->sortEvents($filteredEvents, $sort);
        return $filteredEvents;
    }

    private function sortEvents($filteredEvents, $sort)
    {
        usort($filteredEvents, function ($eventA, $eventB) use ($sort) {
            /** @var Event $eventA */
            /** @var Event $eventB */
            if ($sort == 'DESC') {
                return $eventA->getStartDate() < $eventB->getStartDate();
            }

            return $eventA->getStartDate() > $eventB->getStartDate();
        });
        return $filteredEvents;
    }

    protected function getErrorFlashMessage()
    {
        return false;
    }
}
