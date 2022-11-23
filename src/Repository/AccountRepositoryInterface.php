<?php

namespace App\Repository;

interface AccountRepositoryInterface extends AppRepositoryInterface
{
    public function getCurrentBalance();

    public function getLastRecord();

    public function getAllRecords();
}
