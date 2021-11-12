<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WalletRepository::class)
 * @ORM\Table(name="wallet")
 */
class Wallet extends AbstractWallet
{
}
