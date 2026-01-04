<?php

declare(strict_types=1);

namespace BrainAppeal\CampusEventsFrontend\Domain\Model\Dto;

trait DateRangeTrait
{

    /**
     * Minimum date
     *
     * @var ?string
     */
    protected $minimumDate = '';

    /**
     * Maximum date
     *
     * @var ?string
     */
    protected $maximumDate = '';

    public function getMinimumDate(): ?string
    {
        return $this->minimumDate;
    }

    public function setMinimumDate(?string $minimumDate): void
    {
        $this->minimumDate = $minimumDate;
    }

    public function isDateRangeFilterActive(): bool
    {
        return !empty($this->minimumDate) || !empty($this->maximumDate);
    }

    public function getMaximumDate(): ?string
    {
        if ($this->maximumDate && $this->minimumDate && strtotime($this->maximumDate) < strtotime($this->minimumDate)) {
            return null;
        }
        return $this->maximumDate;
    }

    public function setMaximumDate(?string $maximumDate): void
    {
        $this->maximumDate = $maximumDate;
    }

    public function getMinimumTstamp(): int
    {
        $tstamp = $this->minimumDate ? strtotime($this->minimumDate) : 0;
        return $tstamp ?: 0;
    }

    public function getMaximumTstamp(): int
    {
        $tstamp = $this->getMaximumDate() ? strtotime($this->getMaximumDate()) : 0;
        return $tstamp ?: 0;
    }
}
