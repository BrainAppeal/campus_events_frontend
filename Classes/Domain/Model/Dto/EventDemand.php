<?php

declare(strict_types=1);

namespace BrainAppeal\CampusEventsFrontend\Domain\Model\Dto;

/**
 * Search Demand object which holds all information to get the correct records.
 */
class EventDemand extends AbstractDemand
{
    use DateRangeTrait;

    protected ?string $filterTimespanType = '';
    protected ?string $filterTimespanDateField = '';

    protected ?string $filterCategoryMode = null;
    protected ?array $excludeFilterCategories = [];

    protected ?array $viewLists = [];

    protected int $limit = 0;

    public function getViewLists(): ?array
    {
        return $this->viewLists;
    }

    public function setViewLists(?array $viewLists): void
    {
        $this->viewLists = array_map(intval(...), $viewLists);
    }

    public function getExcludeFilterCategories(): ?array
    {
        return $this->excludeFilterCategories;
    }

    public function setExcludeFilterCategories(?array $excludeFilterCategories): void
    {
        $this->excludeFilterCategories = $excludeFilterCategories;
    }

    public function getFilterCategoryMode(): ?string
    {
        return $this->filterCategoryMode;
    }

    public function setFilterCategoryMode(?string $filterCategoryMode): void
    {
        $this->filterCategoryMode = $filterCategoryMode;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function setTimespanDateLimit(string $timespanType): void
    {
        $this->filterTimespanType = $timespanType;
        $dateToday = date('Y-m-d');
        if ($timespanType === 'future') {
            $minDate = $this->getMinimumDate();
            if (!$minDate || $minDate < $dateToday) {
                $this->setMinimumDate($dateToday);
            }
        } elseif ($timespanType === 'past') {
            $maxDate = $this->getMaximumDate();
            if (!$maxDate || $maxDate > $dateToday) {
                $this->setMaximumDate($dateToday);
            }
        }
    }

    public function getFilterTimespanType(): ?string
    {
        return $this->filterTimespanType;
    }

    public function getFilterTimespanDateField(): ?string
    {
        return $this->filterTimespanDateField;
    }

    public function setFilterTimespanDateField(?string $filterTimespanDateField): void
    {
        $this->filterTimespanDateField = $filterTimespanDateField;
    }
}
