<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\SetUp;

class TransferToBackupCurrencyTest extends ApiTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

    public function testPost(): void
    {
        $this->assertSame(100.00, $this->plnRepository->getCurrentBalance());

        $this->apiClient->request('POST', '/api/transfer/to/backup', [
            'auth_bearer' => $this->token,
            'headers' => [
                'Content-Type' => 'application/ld+json; charset=utf-8',
            ],
            'json' => [
                "currency" => true,
                "amount" => 100,
                "date" => "2023-06-26"
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->assertSame(0.00, $this->plnRepository->getCurrentBalance());

        $this->apiClient->request('GET', '/api/backups', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "member" => [
                [
                    "retiring" => 0,
                    "holiday" => 0,
                    "amount" => 100,
                    "balance" => 0
                ]
            ]
        ]);
    }
}
