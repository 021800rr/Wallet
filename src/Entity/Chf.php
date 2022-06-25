<?php

namespace App\Entity;

use App\Repository\ChfRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChfRepository::class)]
#[ORM\Table(name: "chf")]
class Chf extends AbstractWallet
{
}
