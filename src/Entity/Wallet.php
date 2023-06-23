<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\WalletRepository;
use App\State\WalletProcessor;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
#[ORM\Table(name: 'wallet')]
#[ApiResource(
    normalizationContext: ['groups' => ['account:read']],
    order: ['date' => 'DESC', 'id' => 'DESC']
)]
#[GetCollection]
#[Post(
    denormalizationContext: ['groups' => ['account:create']],
    processor: WalletProcessor::class,
)]
#[Put(
    denormalizationContext: ['groups' => ['account:update']],
    processor: WalletProcessor::class,
)]
#[Patch(denormalizationContext: ['groups' => ['account:patch']], )]
#[Delete(processor: WalletProcessor::class, )]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'amount' => 'exact',
        'balance' => 'exact',
        'contractor.description' => 'ipartial'
    ]
)]
class Wallet extends AbstractWallet
{
}
