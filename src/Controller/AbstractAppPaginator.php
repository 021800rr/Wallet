<?php

namespace App\Controller;

use App\Repository\PaginatorEnum;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractAppPaginator extends AbstractController
{
    public function getPagerfanta(Request $request, QueryBuilder $queryBuilder): Pagerfanta
    {
        $adapter = new QueryAdapter($queryBuilder);

        return Pagerfanta::createForCurrentPageWithMaxPerPage(
            $adapter,
            (int) $request->query->get('page', 1),
            PaginatorEnum::PerPage->value
        );
    }
}
