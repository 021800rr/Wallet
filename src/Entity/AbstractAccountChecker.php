<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

abstract class AbstractAccountChecker
{
    #[Groups(['account:read'])]
    protected string $result = 'Error';

    /** @var AbstractWallet[] */
    #[Groups(['account:read'])]
    protected array $accounts = [];

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    public function getAccounts(): array
    {
        return $this->accounts;
    }

    public function addAccount(AbstractWallet $account): void
    {
        $this->accounts[] = $account;
    }
}
