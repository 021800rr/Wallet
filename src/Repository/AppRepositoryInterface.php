<?php

namespace App\Repository;

interface AppRepositoryInterface extends FeeRepositoryInterface
{
    public function getPaginator(int $offset);
}
