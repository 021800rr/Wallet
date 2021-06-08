<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;

trait Setup
{
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('rr');
        $this->client->loginUser($testUser);
        $this->client->followRedirects();

        parent::setUp();
    }
}
