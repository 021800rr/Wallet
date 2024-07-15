<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Repository\BackupRepository;
use App\State\BackupProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BackupRepository::class)]
#[ORM\HasLifecycleCallbacks()]
#[ApiResource(
    normalizationContext: ['groups' => ['backup:get']],
    order: ['date' => 'DESC', 'id' => 'DESC'],
    security: "is_granted('ROLE_ADMIN')"
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
    public const null INAPPLICABLE = null;
    public const false NOT_PROCESSED = false;
    public const true DONE = true;

    #[ORM\Column(type: 'string', length: 7, nullable: true)]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 7, max: 7)]
    #[Assert\Regex(
        pattern: '/^\d{4}-\d{2}$/',
        message: 'The yearMonth must be in the format YYYY-MM.',
    )]
    private ?string $yearMonth = null;

    #[Groups(['backup:get', 'payments:get'])]
    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'float')]
    private float $retiring = 0.0;

    #[Groups(['backup:get', 'payments:get'])]
    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'float')]
    private float $holiday = 0.0;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Assert\Type(type: 'boolean')]
    private ?bool $interest = null;

    public function __construct()
    {
        parent::__construct();
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

    public function getYearMonth(): ?string
    {
        return $this->yearMonth;
    }

    public function getRetiring(): float
    {
        return $this->retiring;
    }

    public function setRetiring(float $retiring): self
    {
        $this->retiring = (float) number_format($retiring, 2, '.', '');

        return $this;
    }

    public function getHoliday(): float
    {
        return $this->holiday;
    }

    public function setHoliday(float $holiday): self
    {
        $this->holiday = (float) number_format($holiday, 2, '.', '');

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
