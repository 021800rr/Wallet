<?php

namespace App\Entity;

use App\Repository\EurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EurRepository::class)]
#[ORM\Table(name: "eur")]
class Eur extends AbstractWallet
{
}
