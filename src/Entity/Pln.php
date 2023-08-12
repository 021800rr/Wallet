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
use App\Repository\PlnRepository;
use App\State\PlnProcessor;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlnRepository::class)]
#[ORM\Table(name: 'wallet')]
#[ApiResource(
    normalizationContext: ['groups' => ['account:read']],
    order: ['date' => 'DESC', 'id' => 'DESC']
)]
#[GetCollection]
#[Post(
    denormalizationContext: ['groups' => ['account:create']],
    processor: PlnProcessor::class,
)]
#[Put(
    denormalizationContext: ['groups' => ['account:update']],
    processor: PlnProcessor::class,
)]
#[Patch(denormalizationContext: ['groups' => ['account:patch']], )]
#[Delete(processor: PlnProcessor::class, )]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'amount' => 'exact',
        'balance' => 'exact',
        'contractor.description' => 'ipartial'
    ]
)]
class Pln extends AbstractWallet
{
}