<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

// extend by Backup, AbstractWallet extend by Pln, Chf, Eur -> ORM\Entity, ApiResource
abstract class AbstractAccount
{
    #[Groups('account:get')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[Groups(['account:get', 'backup:get', 'payments:get', 'account:post' , 'account:put'])]
    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    #[Assert\Type(type: DateTimeInterface::class)]
    protected DateTimeInterface $date;

    #[Groups(['account:get', 'backup:get', 'payments:get', 'account:post' , 'account:put', 'backup:patch'])]
    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'float')]
    #[Assert\Regex(
        pattern: '/^-?\d+(\.\d{1,2})?$/',
        message: 'The amount must be a valid number with up to 2 decimal places.',
    )]
    protected float $amount;

    #[Groups(['account:get', 'backup:get', 'payments:get'])]
    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'float')]
    protected float $balance = 0.0;

    #[Groups(['account:get', 'account:post' , 'account:put'])]
    #[ORM\ManyToOne(targetEntity: Contractor::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(type: Contractor::class)]
    protected ?Contractor $contractor = null;

    #[Groups(['account:get', 'backup:get', 'payments:get', 'account:post' , 'account:put', 'backup:patch'])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 255)]
    protected ?string $description = null;

    public function __construct()
    {
        $this->date = new DateTime();
    }

    public function getId(): ?int
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
        $this->balance = (float) number_format($balance, 2, '.', '');

        return $this;
    }

    public function getContractor(): ?Contractor
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
