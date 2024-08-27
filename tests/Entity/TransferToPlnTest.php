<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\SetUp;

class TransferToPlnTest extends ApiTestCase
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

        $this->apiClient->request('POST', '/api/transfer/to/pln', [
            'auth_bearer' => $this->token,
            'json' => [
                "amount" => 100,
                "date" => "2023-06-25"
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->assertSame(200.00, $this->plnRepository->getCurrentBalance());

        $this->apiClient->request('GET', '/api/backups', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:member" => [
                [
                    "retiring" => 300,
                    "holiday" => 200,
                    "amount" => -100,
                    "balance" => 500
                ],
            ]
        ]);
    }
}
