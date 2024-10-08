<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

// extend by TransferToPln, TransferToBackup -> ApiResource
abstract class AbstractTransfer
{
    #[Groups('transfer:post')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'float')]
    #[Assert\Regex(
        pattern: '/^-?\d+(\.\d{1,2})?$/',
        message: 'The amount must be a valid number with up to 2 decimal places.',
    )]
    protected ?float $amount;

    #[Groups('transfer:post')]
    #[Assert\NotBlank]
    #[Assert\Type(type: DateTimeInterface::class)]
    protected DateTimeInterface $date;

    public function __construct()
    {
        $this->date = new DateTime();
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
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
}
