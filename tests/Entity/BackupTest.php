<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Backup;
use App\Tests\SetUp;

class BackupTest extends ApiTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

    public function testGetCollection(): void
    {
        $this->apiClient->request('GET', '/api/backups', ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Backup::class);
        $this->assertJsonContains([
            "totalItems" => 3,
            "member" => [
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
        $this->apiClient->request('DELETE', '/api/backups/3', ['auth_bearer' => $this->token]);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            $this->backupRepository->findOneBy(['id' => 3])
        );

        $this->apiClient->request('GET', '/api/backups', ['auth_bearer' => $this->token]);

        $this->assertJsonContains([
            "totalItems" => 2,
            "member" => [
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
        $this->apiClient->request('PATCH', '/api/backups/3', [
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

        $this->apiClient->request('GET', '/api/backups', ['auth_bearer' => $this->token]);

        $this->assertJsonContains([
            "totalItems" => 3,
            "member" => [
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
