<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Repository\BackupRepository;
use App\State\BackupProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BackupRepository::class)]
#[ORM\HasLifecycleCallbacks()]
#[ApiResource(
    normalizationContext: ['groups' => ['backup:read']],
    order: ['date' => 'DESC', 'id' => 'DESC']
)]
#[GetCollection]
#[Patch(
    denormalizationContext: ['groups' => 'backup:patch'],
    processor: BackupProcessor::class,
)]
#[Delete(processor: BackupProcessor::class, )]
class Backup extends AbstractAccount
{
    // boolean interest as const:
    public const INAPPLICABLE = null;
    public const NOT_PROCESSED = false;
    public const DONE = true;

    #[ORM\Column(type: 'string', length: 7)]
    private string $yearMonth;

    #[Groups(['backup:read', 'payments:read'])]
    #[ORM\Column(type: 'float')]
    private float $retiring = 0.0;

    #[Groups(['backup:read', 'payments:read'])]
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
