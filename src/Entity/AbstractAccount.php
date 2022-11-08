<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'date')]
    #[Assert\Type('DateTimeInterface')]
    protected DateTimeInterface $date;

    #[ORM\Column(type: 'float')]
    protected float $amount;

    #[ORM\Column(type: 'float')]
    #[Assert\Type('float')]
    protected float $balance = 0.00;

    #[ORM\ManyToOne(targetEntity: Contractor::class, inversedBy: 'wallets')]
    #[ORM\JoinColumn(nullable: false)]
    protected Contractor $contractor;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $description;

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

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = round($balance, 2);

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
}
