<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Fee;
use App\Tests\SetUp;

class FeeTest extends ApiTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

    public function testPostInsertToPln(): void
    {
        $this->apiClient->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "totalItems" => 5,
        ]);
        $this->apiClient->request('POST', '/api/fees/insert/to/pln', [
            'auth_bearer' => $this->token,
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->apiClient->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "totalItems" => 7,
        ]);
    }

    public function testGetCollection(): void
    {
        $this->apiClient->request('GET', '/api/fees', ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Fee::class);

        $this->assertJsonContains([
            "totalItems" => 2,
            "member" => [
                0 => [
                    "contractor" => [
                        "description" => "Netflix",
                    ],
                ],
                1 => [
                    "contractor" => [
                        "description" => "Spotify",
                    ]
                ]
            ]
        ]);
    }

    public function testPost(): void
    {
        $this->apiClient->request('POST', '/api/fees', [
            'auth_bearer' => $this->token,
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                "date" => 1,
                "amount" => 1,
                "contractor" => "/api/contractors/1"
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "date" => 1,
            "amount" => 1,
            "contractor" => [
                "@id" => "/api/contractors/1",
            ]
        ]);

        $this->apiClient->request('GET', '/api/fees', ['auth_bearer' => $this->token]);

        $this->assertJsonContains([
            "totalItems" => 3,
        ]);
    }

    public function testDelete(): void
    {
        $netflix = $this->contractorRepository->findOneBy(['description' => 'Netflix']);

        $iri = $this->findIriBy(Fee::class, ['contractor' => $netflix]);
        $this->apiClient->request('DELETE', (string) $iri, ['auth_bearer' => $this->token]);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->feeRepository->findOneBy(['contractor' => $netflix]));

        $this->apiClient->request('GET', '/api/fees', ['auth_bearer' => $this->token]);

        $this->assertJsonContains([
            "totalItems" => 1,
        ]);
    }

    public function testPatch(): void
    {
        $netflix = $this->contractorRepository->findOneBy(['description' => 'Netflix']);
        /** @var string $iri */
        $iri = $this->findIriBy(Fee::class, ['contractor' => $netflix]);
        $this->apiClient->request('PATCH', $iri, [
            'auth_bearer' => $this->token,
            'json' => [
                "date" => 1,
                "amount" => 1,
                "contractor" => "/api/contractors/1"
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "date" => 1,
            "amount" => 1,
            "contractor" => [
                "@id" => "/api/contractors/1",
            ]
        ]);
    }
}
