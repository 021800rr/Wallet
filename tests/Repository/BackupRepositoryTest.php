<?php

namespace App\Tests\Repository;

use App\Entity\Backup;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BackupRepositoryTest extends KernelTestCase
{
    use SetupTrait;

    public function testPaymentsByMonth(): void
    {
        $paymentsByMonth = $this->entityManager
            ->getRepository(Backup::class)
            ->paymentsByMonth();

        $this->assertSame("2021-05", $paymentsByMonth[0]['yearMonth']);
        $this->assertSame("600", $paymentsByMonth[0]['sum_of_amount']);
    }
}
