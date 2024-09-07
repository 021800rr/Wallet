<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Chf;
use App\Tests\SetUp;

class ChfTest extends ApiTestCase
{
    use SetUp;

    public function testGetCollection(): void
    {
        $this->apiClient->request('GET', '/api/chfs', ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Chf::class);

        $this->assertJsonContains([
            "hydra:totalItems" => 3,
            "hydra:member" => [
                0 => [
                    "date" => "2021-11-26T00:00:00+00:00",
                    "amount" => 40.04,
                    "balance" => 70.07,
                ],
                1 => [
                    "date" => "2021-11-04T00:00:00+00:00",
                    "amount" => 20.02,
                    "balance" => 30.03,
                ],
                2 => [
                    "date" => "2021-10-30T00:00:00+00:00",
                    "amount" => 10.01,
                    "balance" => 10.01,
                ]
            ]
        ]);
    }

    public function testPost(): void
    {
        $this->apiClient->request('POST', '/api/chfs', [
            'auth_bearer' => $this->token,
            'json' => [
                "date" => "2023-06-26",
                "amount" => 50.05,
                "contractor" => "/api/contractors/5",
                "description" => "test..."
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "date" => "2023-06-26T00:00:00+00:00",
            "amount" => 50.05,
            "contractor" => [
                "@id" => "/api/contractors/5",
            ]
        ]);

        $this->apiClient->request('GET', '/api/chfs', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 4,
            "hydra:member" => [
                0 => [
                    "date" => "2023-06-26T00:00:00+00:00",
                    "balance" => 120.12,
                ]
            ]
        ]);
    }

    public function testPut(): void
    {
        $this->apiClient->request('PUT', '/api/chfs/3', [
            'auth_bearer' => $this->token,
            'json' => [
                "date" => "2021-11-23",
                "amount" => 40,
                "contractor" => "/api/contractors/5",
                "description" => "test test"
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "date" => "2021-11-23T00:00:00+00:00",
            "amount" => 40,
            "contractor" => [
                "id" => 5
            ],
            "description" => "test test"
        ]);

        $this->apiClient->request('GET', '/api/chfs', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 3,
            "hydra:member" => [
                0 => [
                    "balance" => 70.03,
                ],
            ]
        ]);
    }

    public function testDelete(): void
    {
        $this->apiClient->request('DELETE', '/api/chfs/3', ['auth_bearer' => $this->token]);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            $this->chfRepository->findOneBy(['id' => 3])
        );

        $this->apiClient->request('GET', '/api/chfs', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 2,
            "hydra:member" => [
                0 => [
                    "balance" => 30.03,
                ],
            ]
        ]);
    }

    public function testPatch(): void
    {
        $this->apiClient->request('PATCH', '/api/chfs/2', [
            'auth_bearer' => $this->token,
            'json' => [
                "isConsistent" => true,
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "isConsistent" => true,
        ]);
    }

    /**
     * @dataProvider chfProvider
     */
    public function testValidation(Chf $chf, int $expectedViolationCount): void
    {
        $violations = $this->validateEntity($chf);
        $this->assertCount($expectedViolationCount, $violations);
    }

    /**
     * @return array<string, array{0: Chf, 1: int}>
     */
    public function chfProvider(): array
    {
        $chf1 = new Chf();

        return [
            'invalid amount and balanceSupervisor' => [$chf1, 4],
        ];
    }
}
