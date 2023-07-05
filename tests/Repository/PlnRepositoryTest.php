<?php

namespace App\Tests\Repository;

use App\Tests\SetUp;
use Doctrine\ORM\Query\Parameter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlnRepositoryTest extends KernelTestCase
{
    use SetUp;

    public function testFindAll(): void
    {
        $plnTransactions = $this->plnRepository->findAll();
        $this->assertSame(5, count($plnTransactions));
    }

    public function testGetAllRecords(): void
    {
        $plnTransactions = $this->plnRepository->getAllRecords();
        $this->assertSame(5, count($plnTransactions));
    }

    public function testSearch(): void
    {
        $paginator = $this->plnRepository->search('-10', 15);

        /** @var Parameter $parameter */
        $parameter = $paginator->getQuery()->getParameters()[2];
        $this->assertSame("amount", $parameter->getName());
        $this->assertSame(-10.0, $parameter->getValue());
        $this->assertSame(1, $paginator->count());

        $paginator = $this->plnRepository->search('190', 15);

        /** @var Parameter $parameter */
        $parameter = $paginator->getQuery()->getParameters()[3];
        $this->assertSame("balance", $parameter->getName());
        $this->assertSame(190.0, $parameter->getValue());
        $this->assertSame(1, $paginator->count());

        $paginator = $this->plnRepository->search('all', 15);

        /** @var Parameter $parameter */
        $parameter = $paginator->getQuery()->getParameters()[0];
        $this->assertSame("contractor", $parameter->getName());
        $this->assertSame("%all%", $parameter->getValue());
        $this->assertSame(2, $paginator->count());
    }
}
