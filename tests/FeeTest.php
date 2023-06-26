<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Contractor;
use App\Entity\Fee;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class FeeTest extends ApiTestCase
{
    use Setup;

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testPostInsertToWallet(): void
    {
        $this->client->request('GET', '/api/wallets', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 3,
        ]);
        $this->client->request('POST', '/api/fee/insert/to/wallet', ['auth_bearer' => $this->token]);
        $this->assertResponseStatusCodeSame(204);
        $this->client->request('GET', '/api/wallets', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 5,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetCollection(): void
    {
        $this->client->request('GET', '/api/fees', ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Fee::class);

        $this->assertJsonContains([
            "hydra:totalItems" => 2,
            "hydra:member" => [
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

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testPost(): void
    {
        $this->client->request('POST', '/api/fees', [
            'auth_bearer' => $this->token,
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

        $this->client->request('GET', '/api/fees', ['auth_bearer' => $this->token]);

        $this->assertJsonContains([
            "hydra:totalItems" => 3,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDelete(): void
    {
        $netflix = static::getContainer()->get('doctrine')->getRepository(Contractor::class)->findOneBy(['description' => 'Netflix']);

        $iri = $this->findIriBy(Fee::class, ['contractor' => $netflix]);
        $this->client->request('DELETE', $iri, ['auth_bearer' => $this->token]);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Fee::class)->findOneBy(['contractor' => $netflix])
        );

        $this->client->request('GET', '/api/fees', ['auth_bearer' => $this->token]);

        $this->assertJsonContains([
            "hydra:totalItems" => 1,
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
        $netflix = static::getContainer()->get('doctrine')->getRepository(Contractor::class)->findOneBy(['description' => 'Netflix']);
        $iri = $this->findIriBy(Fee::class, ['contractor' => $netflix]);
        $this->client->request('PATCH', $iri, [
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
