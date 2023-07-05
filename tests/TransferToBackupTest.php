<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TransferToBackupTest extends ApiTestCase
{
    use SetupApi;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     * @see Service/Transfer/TransferTest.php
     */
    public function testPost(): void
    {
        $this->assertSame(100.00, $this->plnRepository->getCurrentBalance());

        $this->client->request('POST', '/api/transfer/to/backup', [
            'auth_bearer' => $this->token,
            'json' => [
                "currency" => false,
                "amount" => 100,
                "date" => "2023-06-24"
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->assertSame(0.00, $this->plnRepository->getCurrentBalance());

        $this->client->request('GET', '/api/backups', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:member" => [
                [
                    "retiring" => 350,
                    "holiday" => 350,
                    "amount" => 100,
                    "balance" => 700
                ],
            ]
        ]);
    }
}
