<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class AbstractWallet extends AbstractAccount
{
    #[ORM\Column(type: 'boolean', nullable: true)]
    protected ?bool $isConsistent;

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
