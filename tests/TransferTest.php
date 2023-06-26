<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Repository\BackupRepository;
use App\Repository\WalletRepository;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TransferTest extends ApiTestCase
{
    use Setup;

    /**
     * @see Service/Transfer/TransferTest.php
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    public function testPost(): void
    {
        $walletRepository = static::getContainer()->get(WalletRepository::class);
        $backupRepository = static::getContainer()->get(BackupRepository::class);

        $this->client->request('POST', '/api/transfer/to/backup', [
            'auth_bearer' => $this->token,
            'json' => [
                "currency" => false,
                "amount" => 100,
                "date" => "2023-06-26"
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->assertSame(70.00, $walletRepository->getCurrentBalance());

        $transactions = $backupRepository->findAll();
        $this->assertSame(700.00, $transactions[0]->getBalance());

        $this->client->request('POST', '/api/transfer/to/wallet', [
            'auth_bearer' => $this->token,
            'json' => [
                "amount" => 100,
                "date" => "2023-06-26"
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->assertSame(170.00, $walletRepository->getCurrentBalance());

        $transactions = $backupRepository->findAll();
        $this->assertSame(350.00, $transactions[0]->getRetiring());
        $this->assertSame(250.00, $transactions[0]->getHoliday());
        $this->assertSame(600.00, $transactions[0]->getBalance());

        $this->client->request('POST', '/api/transfer/to/backup', [
            'auth_bearer' => $this->token,
            'json' => [
                "currency" => true,
                "amount" => 100,
                "date" => "2023-06-26"
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $walletRepository = static::getContainer()->get(WalletRepository::class);
        $this->assertSame(70.00, $walletRepository->getCurrentBalance());

        $backupRepository = static::getContainer()->get(BackupRepository::class);
        $transactions = $backupRepository->findAll();
        $this->assertSame(0.00, $transactions[0]->getBalance());
    }
}
