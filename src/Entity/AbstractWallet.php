<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

// extend by Pln, Chf, Eur -> ORM\Entity, ApiResource
abstract class AbstractWallet extends AbstractAccount
{
    #[Groups(['account:get', 'account:patch'])]
    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Assert\Type(type: 'boolean')]
    protected ?bool $isConsistent = null;

    #[Groups('account:get')]
    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\Type(type: 'float')]
    protected ?float $balanceSupervisor = null;

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
        return $this->balanceSupervisor;
    }

    public function setBalanceSupervisor(?float $balanceSupervisor): self
    {
        if (is_numeric($balanceSupervisor)) {
            $this->balanceSupervisor = (float) number_format((float) $balanceSupervisor, 2, '.', '');
        } elseif(is_null($balanceSupervisor)) {
            $this->balanceSupervisor = $balanceSupervisor;
        } else {
            throw new InvalidArgumentException('balanceSupervisor must be a float or int.');
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getId() . ' : ' .
            $this->getDate()->format('Y-m-d') . ' : ' .
            $this->getAmount() . ' : ' .
            $this->getBalance() . ' : ' .
            $this->getBalanceSupervisor() . ' : ' .
            $this->getContractor()?->getDescription() .
            ($this->getDescription() ? ' : ' . $this->getDescription() : '');
    }
}
