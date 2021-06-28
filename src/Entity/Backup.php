<?php

namespace App\Entity;

use App\Repository\BackupRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BackupRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Backup
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $date;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private string $yearMonth;

    /**
     * @ORM\Column(type="float")
     */
    private float $amount;

    /**
     * @ORM\Column(type="float")
     */
    private float $retiring = 0.0;

    /**
     * @ORM\Column(type="float")
     */
    private float $holiday = 0.0;

    /**
     * @ORM\Column(type="float")
     */
    private float $balance = 0.0;

    /**
     * @ORM\ManyToOne(targetEntity=Contractor::class, inversedBy="backups")
     * @ORM\JoinColumn(nullable=false)
     */
    private Contractor $contractor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isConsistent;

    public function __construct()
    {
        $this->date = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
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

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
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

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getContractor(): Contractor
    {
        return $this->contractor;
    }

    public function setContractor(Contractor $contractor): self
    {
        $this->contractor = $contractor;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsConsistent(): ?bool
    {
        return $this->isConsistent;
    }

    public function setIsConsistent(?bool $isConsistent): self
    {
        $this->isConsistent = $isConsistent;

        return $this;
    }
}
