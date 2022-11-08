<?php

namespace App\Entity;

use App\Repository\BackupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BackupRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Backup extends AbstractAccount
{
    // boolean interest as const:
    public const INAPPLICABLE = null;
    public const NOT_PROCESSED = false;
    public const DONE = true;

    #[ORM\Column(type: 'string', length: 7)]
    private string $yearMonth;

    #[ORM\Column(type: 'float')]
    private float $retiring = 0.0;

    #[ORM\Column(type: 'float')]
    private float $holiday = 0.0;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $interest;

    public function __construct()
    {
        parent::__construct();

        $this->interest = null;
    }

    public function computeShortDate(): self
    {
        $this->yearMonth = $this->date->format('Y-m');

        return $this;
    }

    public function setYearMonth(string $date): self
    {
        $this->yearMonth = $date;

        return $this;
    }

    public function getYearMonth(): string
    {
        return $this->yearMonth;
    }

    public function getRetiring(): float
    {
        return $this->retiring;
    }

    public function setRetiring(float $retiring): self
    {
        $this->retiring = $retiring;

        return $this;
    }

    public function getHoliday(): float
    {
        return $this->holiday;
    }

    public function setHoliday(float $holiday): self
    {
        $this->holiday = $holiday;

        return $this;
    }

    public function getInterest(): ?bool
    {
        return $this->interest;
    }

    public function setInterest(?bool $interest): self
    {
        $this->interest = $interest;

        return $this;
    }
}
