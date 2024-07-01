<?php

namespace App\Entity;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

// extend by PlnWalletChecker, ChfWalletChecker, EurWalletChecker -> ApiResource
abstract class AbstractWalletChecker
{
    #[Groups(['account:get'])]
    #[Assert\Type(type: 'string')]
    protected string $result = 'Error';

    /** @var AbstractWallet[] */
    #[Groups(['account:get'])]
    #[Assert\Type(type: 'array')]
    protected array $wallets = [];

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    /** @return AbstractWallet[] */
    public function getWallets(): array
    {
        return $this->wallets;
    }

    public function addWallet(AbstractWallet $wallet): void
    {
        $this->wallets[] = $wallet;
    }
}
