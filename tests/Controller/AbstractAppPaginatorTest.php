<?php

namespace App\Tests\Controller;

use App\Config\PaginatorEnum;
use App\Controller\AbstractAppPaginator;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Pagerfanta;
use Rector\Enum\ClassName;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class AbstractAppPaginatorTest extends WebTestCase
{
    /**
     * @dataProvider pageParamProvider
     */
    public function testGetPagerfanta(?string $pageParam, int $expectedPage): void
    {
        $request = new Request(['page' => $pageParam]);

        $queryBuilder = $this->createMock(QueryBuilder::class);

        $paginator = new class extends AbstractAppPaginator {
            /**
             * @param Request $request
             * @param QueryBuilder $queryBuilder
             * @return Pagerfanta<ClassName>
             */
            public function getPagerfantaPublic(Request $request, QueryBuilder $queryBuilder): Pagerfanta
            {
                return $this->getPagerfanta($request, $queryBuilder);
            }
        };

        $pagerfanta = $paginator->getPagerfantaPublic($request, $queryBuilder);

        $this->assertInstanceOf(Pagerfanta::class, $pagerfanta);
        $this->assertEquals($expectedPage, $pagerfanta->getCurrentPage());
        $this->assertEquals(PaginatorEnum::PerPage->value, $pagerfanta->getMaxPerPage());
    }

    /**
     * @return array<string, array<int, int|string|null>>
     */
    public function pageParamProvider(): array
    {
        return [
            'valid page param' => ['1', 1],
            'no page param' => [null, 1],
            'negative page param' => ['-1', 1],
            'zero page param' => ['0', 1],
            'non-numeric page param' => ['invalid', 1],
        ];
    }
}
