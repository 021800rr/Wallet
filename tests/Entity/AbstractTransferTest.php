<?php

namespace App\Tests\Entity;

use App\Entity\AbstractTransfer;
use App\Tests\SetUp;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractTransferTest extends KernelTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

    public function testSetAmountWithInvalidValueThrowsException(): void
    {
        $transfer = new class() extends AbstractTransfer {};
        $transfer->setAmount(123.456);
        $violations = $this->validateEntity($transfer);

        $this->assertCount(1, $violations, 'Invalid amount, required "date" field missing.');

        $transfer = new class() extends AbstractTransfer {};
        $violations = $this->validateEntity($transfer);

        $this->assertCount(1 , $violations, 'Missing required fields should cause two violations.');
    }
}
