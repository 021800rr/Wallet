<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Backup;
use App\Tests\SetUp;

class BackupTest extends ApiTestCase
{
    use SetUp;

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
