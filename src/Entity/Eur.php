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
    normalizationContext: ['groups' => ['account:read']],
    order: ['date' => 'DESC', 'id' => 'DESC']
)]
#[GetCollection]
#[Post(
    denormalizationContext: ['groups' => ['account:create']],
    processor: EurProcessor::class,
)]
#[Put(
    denormalizationContext: ['groups' => ['account:update']],
    processor: EurProcessor::class,
)]
#[Patch(denormalizationContext: ['groups' => ['account:patch']], )]
#[Delete(processor: EurProcessor::class, )]
class Eur extends AbstractWallet
{
}
