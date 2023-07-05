<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Backup;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class BackupTest extends ApiTestCase
{
    use SetupApi;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetCollection(): void
    {
        $this->client->request('GET', '/api/backups', ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Backup::class);

        $this->assertJsonContains([
            "hydra:totalItems" => 3,
            "hydra:member" => [
                0 => [
                    "amount" => 300,
                    "balance" => 600,
                ],
                1 => [
                    "@id" => "/api/backups/2",
                    "amount" => 200,
                    "balance" => 300,
                ],
                2 => [
                    "amount" => 100,
                    "balance" => 100,
                ]
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testDelete(): void
    {
        $this->client->request('DELETE', '/api/backups/3', ['auth_bearer' => $this->token]);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            $this->backupRepository->findOneBy(['id' => 3])
        );

        $this->client->request('GET', '/api/backups', ['auth_bearer' => $this->token]);

        $this->assertJsonContains([
            "hydra:totalItems" => 2,
            "hydra:member" => [
                0 => [
                    "amount" => 200,
                    "balance" => 300,
                ],
                1 => [
                    "amount" => 100,
                    "balance" => 100,
                ]
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testPatch(): void
    {
        $this->client->request('PATCH', '/api/backups/3', [
            'auth_bearer' => $this->token,
            'json' => [
                "amount" => 400,
                "description" => "string"
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "amount" => 400,
            "description" => "string"
        ]);

        $this->client->request('GET', '/api/backups', ['auth_bearer' => $this->token]);

        $this->assertJsonContains([
            "hydra:totalItems" => 3,
            "hydra:member" => [
                0 => [
                    "amount" => 400,
                    "balance" => 700,
                    "description" => "string"
                ],
                1 => [
                    "@id" => "/api/backups/2",
                    "amount" => 200,
                    "balance" => 300,
                ],
                2 => [
                    "amount" => 100,
                    "balance" => 100,
                ]
            ]
        ]);
    }
}
