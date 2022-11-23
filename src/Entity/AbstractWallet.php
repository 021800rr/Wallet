<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractWallet extends AbstractAccount
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    protected ?bool $isConsistent;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\Type('float')]
    protected ?float $balance_supervisor;

    public function getIsConsistent(): ?bool
    {
        return $this->isConsistent;
    }

    public function setIsConsistent(?bool $isConsistent): self
    {
        $this->isConsistent = $isConsistent;

        return $this;
    }

    public function getBalanceSupervisor(): ?float
    {
        return $this->balance_supervisor;
    }

    public function setBalanceSupervisor(?float $balance_supervisor): self
    {
        $this->balance_supervisor = $balance_supervisor;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getId() . ' : ' .
            $this->getDate()->format('Y-m-d') . ' : ' .
            $this->getAmount() . ' : ' .
            $this->getBalance() . ' : ' .
            $this->getBalanceSupervisor() . ' : ' .
            $this->getContractor()->getDescription() .
            ($this->getDescription() ? ' : ' . $this->getDescription() : '')
            ;
    }
}
