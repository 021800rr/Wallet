<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\PaymentByMonthProvider;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['payments:read']],
)]
#[Get(
    uriTemplate: '/backups/payments/by/month',
    provider: PaymentByMonthProvider::class,
)]
class PaymentsByMonth
{
    /** @var array<int, array<string, string>>|null */
    #[Groups(['payments:read'])]
    private ?array $backups;

    #[Groups(['payments:read'])]
    private ?float $expected;

    #[Groups(['payments:read'])]
    private ?float $plnBalance;

    #[Groups(['payments:read'])]
    private ?float $chfBalance;

    #[Groups(['payments:read'])]
    private ?float $eurBalance;

    #[Groups(['payments:read'])]
    private ?Backup $backupLastRecord;

    #[Groups(['payments:read'])]
    private ?float $total;

    /** @return array<int, array<string, string>>|null */
    public function getBackups(): ?array
    {
        return $this->backups;
    }

    /**
     * @param array<int, array<string, string>> $backups
     */
    public function setBackups(array $backups): void
    {
        $this->backups = $backups;
    }

    public function getExpected(): ?float
    {
        return $this->expected;
    }

    public function setExpected(float $expected): void
    {
        $this->expected = $expected;
    }

    public function getPlnBalance(): ?float
    {
        return $this->plnBalance;
    }

    public function setPlnBalance(float $plnBalance): void
    {
        $this->plnBalance = $plnBalance;
    }

    public function getChfBalance(): ?float
    {
        return $this->chfBalance;
    }

    public function setChfBalance(float $chfBalance): void
    {
        $this->chfBalance = $chfBalance;
    }

    public function getEurBalance(): ?float
    {
        return $this->eurBalance;
    }

    public function setEurBalance(float $eurBalance): void
    {
        $this->eurBalance = $eurBalance;
    }

    public function getBackupLastRecord(): ?Backup
    {
        return $this->backupLastRecord;
    }

    public function setBackupLastRecord(Backup $backupLastRecord): void
    {
        $this->backupLastRecord = $backupLastRecord;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }
}
