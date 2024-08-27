<?php

namespace App\Tests\Repository;

use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BackupRepositoryTest extends KernelTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

    public function testPaymentsByMonth(): void
    {
        $paymentsByMonth = $this->backupRepository->paymentsByMonth();

        $this->assertSame("2021-06", $paymentsByMonth[0]['yearMonth']);
        $this->assertSame("300", $paymentsByMonth[0]['sum_of_amounts']);
    }
}
