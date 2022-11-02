<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
#[ORM\Table(name: 'wallet')]
class Wallet extends AbstractWallet
{
    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\Type('float')]
    private ?float $balance_supervisor;

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
