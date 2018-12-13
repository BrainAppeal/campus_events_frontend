<?php
namespace BrainAppeal\BrainEventFrontend\Controller;

/***
 *
 * This file is part of the "ICF Stocks" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 Gert Hammes <gert.hammes@brain-appeal.com>, Brain Appeal GmbH
 *
 ***/

use BrainAppeal\BrainEventConnector\Domain\Model\Event;
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
     * @var \BrainAppeal\BrainEventConnector\Domain\Repository\EventRepository
     */
    protected $eventRepository = null;

    /**
     * Inject a event repository to enable DI
     *
     * @param \BrainAppeal\BrainEventConnector\Domain\Repository\EventRepository $eventRepository
     */
    public function injectEventRepository(\BrainAppeal\BrainEventConnector\Domain\Repository\EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
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
        $events = $this->eventRepository->findListByPid($pidList, []);
        if ($timespan !== 'all') {
            $events = $this->filterListAfterTimespan($events,$timespan);
            $events = array_slice($events,0,$limit);
        }
        $this->view->assign('events', $events);
        $this->view->assign('contentData', $cObj->data);
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
     * @param \BrainAppeal\BrainEventConnector\Domain\Model\Event $event
     * 
     * @return void
     */
    public function showAction(\BrainAppeal\BrainEventConnector\Domain\Model\Event $event) {
        $this->view->assign('event', $event);
    }

    private function filterListAfterTimespan($events, $timespan) {
        $currentDate = new \DateTime();
        $newEventList = [];
        $startDates = [];
        foreach ($events as $eventKey => $event) {
            /** @var Event $event */
            $startDate = $event->getStartDate();
            $endDate = $event->getEndDate();
            if ($timespan === 'past') {
                if ($startDate > $currentDate) {
                    unset($events[$eventKey]);
                } else {
                    $newEventList[] = $event;
                    $startDates[] = $startDate->getTimestamp();
                }
            } else if ($timespan === 'future') {
                if ($endDate < $currentDate) {
                    unset($events[$eventKey]);
                } else {
                    $newEventList[] = $event;
                    $startDates[] = $startDate->getTimestamp();
                }
            }
        }
        array_multisort($startDates,$newEventList);
        return $newEventList;
    }

    protected function getErrorFlashMessage()
    {
        return false;
    }
}
