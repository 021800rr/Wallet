<?php

namespace App\Tests\Entity;

use App\Entity\AbstractAccount;
use App\Entity\Contractor;
use App\Tests\SetUp;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AbstractAccountTest extends KernelTestCase
{
    use SetUp;

    public function testConstructAndDefaultValues(): void
    {
        $account = new class() extends AbstractAccount {};

        $currentTime = new DateTime();
        $this->assertEquals($currentTime->format('Y-m-d'), $account->getDate()->format('Y-m-d'));
        $this->assertEquals(0.0, $account->getBalance());
        $this->assertNull($account->getContractor());
        $this->assertNull($account->getDescription());
    }

    public function testSetBalanceRoundsToTwoDecimalPlaces(): void
    {
        $account = new class() extends AbstractAccount {};

        $account->setBalance(0.123);
        $this->assertEquals(0.12, $account->getBalance());
    }

    /**
     * @dataProvider accountDataProvider
     */
    public function testValidation(AbstractAccount $account, int $expectedViolationCount): void
    {
        $violations = $this->validateEntity($account);
        $this->assertCount($expectedViolationCount, $violations);
    }

    /**
     * @return array<string, array{0: AbstractAccount, 1: int}>
     */
    public function accountDataProvider(): array
    {
        $validAccount = new class() extends AbstractAccount {};
        $validAccount->setAmount(1.1);
        $validAccount->setContractor(new Contractor());

        $missingFieldsAccount = new class() extends AbstractAccount {};

        $invalidAmountAccount = new class() extends AbstractAccount {};
        $invalidAmountAccount->setAmount(0.123);

        return [
            'valid account' => [$validAccount, 0],
            'missing required fields' => [$missingFieldsAccount, 2],
            'invalid amount format' => [$invalidAmountAccount, 2],
        ];
    }
}
