<?php

namespace App\Entity;

use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;

abstract class AbstractTransfer
{
    #[Groups('transfer:create')]
    protected float $amount;

    #[Groups('transfer:create')]
    protected DateTimeInterface $date;

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): void
    {
        $this->date = $date;
    }
}
