<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

abstract class AbstractWalletChecker
{
    #[Groups(['account:read'])]
    protected string $result = 'Error';

    /** @var AbstractWallet[] */
    #[Groups(['account:read'])]
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
