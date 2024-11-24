<?php

namespace App\Controller;

use App\Config\PaginatorEnum;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractAppPaginator extends AbstractController
{
    /** @phpstan-ignore-next-line */
    public function getPagerfanta(Request $request, QueryBuilder $queryBuilder): Pagerfanta
    {
        $page = (int) $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $adapter = new QueryAdapter($queryBuilder);

        return Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            $page,
            PaginatorEnum::PerPage->value
        );
    }
}
