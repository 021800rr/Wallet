<?php

namespace App\Tests\Repository;

use App\Tests\SetUp;
use Doctrine\ORM\Query\Parameter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlnRepositoryTest extends KernelTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

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
        $pager = $this->plnRepository->search('-10');

        /** @var Parameter $parameter */
        $parameter = $pager->getQuery()->getParameters()[2];
        $this->assertSame("amount", $parameter->getName());
        $this->assertSame(-10.0, $parameter->getValue());
        $this->assertSame(1, count((array) $pager->getQuery()->getResult()));

        $pager = $this->plnRepository->search('190');

        /** @var Parameter $parameter */
        $parameter = $pager->getQuery()->getParameters()[3];
        $this->assertSame("balance", $parameter->getName());
        $this->assertSame(190.0, $parameter->getValue());
        $this->assertSame(1, count((array) $pager->getQuery()->getResult()));

        $pager = $this->plnRepository->search('all');

        /** @var Parameter $parameter */
        $parameter = $pager->getQuery()->getParameters()[0];
        $this->assertSame("contractor", $parameter->getName());
        $this->assertSame("%all%", $parameter->getValue());
        $this->assertSame(2, count((array) $pager->getQuery()->getResult()));
    }
}
