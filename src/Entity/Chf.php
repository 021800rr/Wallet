<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ChfRepository;
use App\State\ChfProcessor;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChfRepository::class)]
#[ORM\Table(name: 'chf')]
#[ApiResource(
    normalizationContext: ['groups' => ['account:get']],
    order: ['date' => 'DESC', 'id' => 'DESC'],
    security: "is_granted('ROLE_ADMIN')"
)]
#[GetCollection]
#[Post(
    denormalizationContext: ['groups' => ['account:post']],
    processor: ChfProcessor::class,
)]
#[Put(
    denormalizationContext: ['groups' => ['account:put']],
    processor: ChfProcessor::class,
)]
#[Patch(denormalizationContext: ['groups' => ['account:patch']], )]
#[Delete(processor: ChfProcessor::class, )]
class Chf extends AbstractWallet
{
}
