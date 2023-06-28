<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Repository\WalletRepository;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TransferToBackupTest extends ApiTestCase
{
    use Setup;

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
        $walletRepository = static::getContainer()->get(WalletRepository::class);
        $this->assertSame(170.00, $walletRepository->getCurrentBalance());

        $this->client->request('POST', '/api/transfer/to/backup', [
            'auth_bearer' => $this->token,
            'json' => [
                "currency" => false,
                "amount" => 100,
                "date" => "2023-06-24"
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->assertSame(70.00, $walletRepository->getCurrentBalance());

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
