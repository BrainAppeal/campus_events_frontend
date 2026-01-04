<?php

declare(strict_types=1);

namespace BrainAppeal\CampusEventsFrontend\Domain\Model\Dto;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Search Demand object which holds all information to get the correct records.
 */
abstract class AbstractDemand
{
    use StoragePageTrait;
    protected const DEFAULT_SORT_BY = 'asc';

    /**
     * Basic search word
     */
    protected ?string $searchTerm = '';

    /**
     * @var string
     */
    protected $sortBy = self::DEFAULT_SORT_BY;

    protected array $allowedSortByChoices = ['relevance', 'asc', 'desc'];

    protected int $currentLanguageId = 0;

    /**
     * Get the subject
     */
    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    public function setSearchTerm(string $searchTerm): void
    {
        $this->searchTerm = $searchTerm;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function setSortBy(?string $sortBy): void
    {
        if (in_array($sortBy, $this->allowedSortByChoices, true)) {
            $this->sortBy = $sortBy;
        }
    }

    public function getCurrentLanguageId(): int
    {
        return $this->currentLanguageId;
    }

    public function setCurrentLanguageId(int $currentLanguageId): void
    {
        $this->currentLanguageId = $currentLanguageId;
    }
}
