<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\EurRepository;
use App\State\EurProcessor;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EurRepository::class)]
#[ORM\Table(name: 'eur')]
#[ApiResource(
    normalizationContext: ['groups' => ['account:get']],
    order: ['date' => 'DESC', 'id' => 'DESC'],
    security: "is_granted('ROLE_ADMIN')"
)]
#[GetCollection]
#[Post(
    denormalizationContext: ['groups' => ['account:post']],
    processor: EurProcessor::class,
)]
#[Put(
    denormalizationContext: ['groups' => ['account:put']],
    processor: EurProcessor::class,
)]
#[Patch(denormalizationContext: ['groups' => ['account:patch']], )]
#[Delete(processor: EurProcessor::class, )]
class Eur extends AbstractWallet
{
}
