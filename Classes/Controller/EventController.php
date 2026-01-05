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
use BrainAppeal\CampusEventsFrontend\Domain\Model\Dto\EventDemand;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Cache\CacheTag;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * EventController
 */
class EventController extends ActionController
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
            if ($cacheDataCollector && class_exists(CacheTag::class)) {
                $cacheDataCollector->addCacheTags(...array_map(fn(string $tag) => new CacheTag($tag), ['tx_campus_events']));
                self::$cacheTagsSet = true;
                // TYPO3 12
            } elseif (($GLOBALS['TSFE'] ?? null) instanceof TypoScriptFrontendController) {
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
     * @return ResponseInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function listAction(): ResponseInterface
    {
        $demand = GeneralUtility::makeInstance(EventDemand::class);
        $this->initializeDemandFromSettings($demand);

        /** @var ContentObjectRenderer $cObj */
        $cObj = $this->request->getAttribute('currentContentObject');

        $languageId = (int)$this->request->getAttribute('language')?->getLanguageId();
        $demand->setCurrentLanguageId($languageId);
        $pidList = $this->settings['startingpoint'] ?? null;
        $constraints = $this->createListConstraintsBasedOnSettings($demand);
        $orderByField = ($this->settings['orderBy'] ?? null) === 'end_tstamp' ? 'endTstamp' : 'startTstamp';
        $orderByDirection = ($this->settings['orderDirection'] ?? null) === 'desc' ? 'DESC' : 'ASC';
        $orderBy = [$orderByField => $orderByDirection];
        $events = $this->eventRepository->findListByPid($pidList, $constraints, $demand->getLimit(), $orderBy);
        $assignedValues = [
            'events' => $events,
            'contentData' => $cObj->data,
            'settings' => $this->settings,
        ];
        $this->view->assignMultiple($assignedValues);
        return $this->htmlResponse();
    }

    /**
     * Initializes the event search demand based on the provided settings.
     *
     * @param EventDemand $search The event search instance to be configured.
     */
    protected function initializeDemandFromSettings(EventDemand $search): void
    {
        $filterCategoryMode = $this->settings['filterCategoryMode'] ?? '';
        $search->setFilterCategoryMode($filterCategoryMode);
        $excludeFilterCategories = [];
        if (isset($this->settings['excludeFilterCategories'])) {
            $excludeFilterCategories = GeneralUtility::intExplode(',', $this->settings['excludeFilterCategories'], true);
        }
        $search->setExcludeFilterCategories($excludeFilterCategories);
        $viewLists = GeneralUtility::intExplode(',', (string)($this->settings['viewLists'] ?? ''), true);
        if (!empty($viewLists)) {
            $search->setViewLists(array_merge($search->getViewLists(), $viewLists));
        }
        $search->setStoragePage((string)($this->settings['storagePage'] ?? ''));
        $search->setLimit((int)($this->settings['limit'] ?? 0));
        $search->setFilterTimespanDateField($this->settings['timespanDateField'] ?? '');
        $search->setTimespanDateLimit($this->settings['timespan'] ?? '');
    }

    /**
     * Creates a list of constraints based on the provided EventDemand settings.
     *
     * This method generates a set of constraints for querying events based on various
     * parameters such as excluded filter categories, view list IDs, minimum and
     * maximum timestamps, and search terms provided in the EventDemand object.
     *
     * @param EventDemand $demand The EventDemand object containing the filtering settings,
     *                            such as excluded categories, view lists, time spans, and search terms.
     * @return array An array of query constraints generated based on the settings in the EventDemand object.
     */
    protected function createListConstraintsBasedOnSettings(EventDemand $demand): array
    {
        $andConstraints = [];
        $query = $this->eventRepository->createQuery();
        $excludeFilterCategories = $demand->getExcludeFilterCategories();
        $viewListIdList = $demand->getViewLists();
        if (!empty($excludeFilterCategories)) {
            if ($demand->getFilterCategoryMode() === 'include') {
                $filterCategoryConstraints = [];
                foreach ($excludeFilterCategories as $excludeFilterCategory) {
                    $filterCategoryConstraints[] = $query->contains('filterCategories', $excludeFilterCategory);
                }
                $andConstraints[] = $query->logicalOr(...$filterCategoryConstraints);
            } else {
                $andConstraints[] = $query->logicalNot($query->contains('filterCategories', $excludeFilterCategories));
            }
        }
        if (!empty($viewListIdList)) {
            $viewListsConstraints = [];
            foreach ($viewListIdList as $viewList) {
                $viewListsConstraints[] = $query->contains('viewLists', $viewList);
            }
            $andConstraints[] = $query->logicalOr(...$viewListsConstraints);
        }
        if ($minTstamp = $demand->getMinimumTstamp()) {
            $field = 'startTstamp';
            if ($demand->getFilterTimespanDateField() === 'end_tstamp') {
                $field = 'endTstamp';
            }
            $andConstraints[] = $query->greaterThanOrEqual($field, $minTstamp);
        }
        if ($maxTstamp = $demand->getMaximumTstamp()) {
            $field = 'endTstamp';
            if ($demand->getFilterTimespanDateField() === 'start_tstamp') {
                $field = 'startTstamp';
            }
            $andConstraints[] = $query->lessThanOrEqual($field, $maxTstamp);
        }
        $searchWords = GeneralUtility::trimExplode(' ', trim(strip_tags((string)$demand->getSearchTerm())), true);
        if (!empty($searchWords)) {
            $searchFields = ['name', 'shortDescription'];
            $subConstraints = [];
            foreach ($searchFields as $searchField) {
                foreach ($searchWords as $searchWord) {
                    $subConstraints[] = $query->like($searchField, '%' . $searchWord . '%');
                }
            }
            $andConstraints[] = $query->logicalOr(...$subConstraints);
        }
        return $andConstraints;
    }

    /**
     * action show
     *
     *
     * @param ?Event $event event item
     * @return ResponseInterface
     */
    public function showAction(?Event $event = null): ResponseInterface
    {
        if ($event === null && ($eventId = (int)($this->settings['event'] ?? 0)) > 0) {
            /** @noinspection CallableParameterUseCaseInTypeContextInspection */
            $event = $this->eventRepository->findByUid($eventId);
        }
        /** @var ContentObjectRenderer $cObj */
        $cObj = $this->request->getAttribute('currentContentObject');
        $assignedValues = [
            'event' => $event,
            'contentObjectData' => $cObj->data,
            'settings' => $this->settings,
        ];
        $this->view->assignMultiple($assignedValues);
        $GLOBALS['TSFE']->addCacheTags(['tx_campus_events_' . $event->getUid()]);
        return $this->htmlResponse();
    }

    protected function getErrorFlashMessage(): bool|string
    {
        return false;
    }
}
