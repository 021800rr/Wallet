<?php

namespace App\Tests\Controller;

use App\Entity\Contractor;
use App\Repository\ContractorRepository;
use App\Repository\UserRepository;

trait Setup
{
    protected Contractor $contractor;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByUsername('rr');
        $this->client->loginUser($testUser);
        $this->client->followRedirects();

        $contractorRepository = static::getContainer()->get(ContractorRepository::class);
        $this->contractor = $contractorRepository->getInternalTransferOwner();

        parent::setUp();
    }
}
