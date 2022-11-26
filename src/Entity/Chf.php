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
    normalizationContext: ['groups' => ['account:read']],
    order: ['date' => 'DESC', 'id' => 'DESC']
)]
#[GetCollection]
#[Post(
    denormalizationContext: ['groups' => ['account:create']],
    processor: ChfProcessor::class,
)]
#[Put(
    denormalizationContext: ['groups' => ['account:update']],
    processor: ChfProcessor::class,
)]
#[Patch(denormalizationContext: ['groups' => ['account:patch']],)]
#[Delete(processor: ChfProcessor::class,)]
class Chf extends AbstractWallet
{
}
