<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\TransferProcessor;

#[ApiResource]
#[Post(
    uriTemplate: '/transfer/to/pln',
    denormalizationContext: ['groups' => ['transfer:create']],
    processor: TransferProcessor::class,
)]
class TransferToPln extends AbstractTransfer
{
}
