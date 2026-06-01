<?php

declare(strict_types=1);

namespace BrainAppeal\CampusEventsFrontend\Domain\Model\Dto;

/**
 * Storage page trait
 */
trait StoragePageTrait
{
    /** @var string */
    protected $storagePage = '';

    /**
     * Set list of storage pages
     *
     * @param string $storagePage storage page list
     */
    public function setStoragePage(string $storagePage): void
    {
        $this->storagePage = $storagePage;
    }

    /**
     * Get list of storage pages
     */
    public function getStoragePage(): string
    {
        return $this->storagePage;
    }
}
