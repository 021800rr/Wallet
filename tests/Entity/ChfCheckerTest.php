<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\SetUp;

class ChfCheckerTest extends ApiTestCase
{
    use SetUp;

    public function testGet(): void
    {
        $this->client->request('GET', '/api/check/chfs', ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "result" => "Passed"
        ]);
    }
}
