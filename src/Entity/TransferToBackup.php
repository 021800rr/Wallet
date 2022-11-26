<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\TransferProcessor;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
#[Post(
    uriTemplate: '/transfer/to/backup',
    denormalizationContext: ['groups' => ['transfer:create']],
    processor: TransferProcessor::class,
)]
class TransferToBackup extends AbstractTransfer
{
    #[Groups('transfer:create')]
    private bool $currency;

    public function isCurrency(): bool
    {
        return $this->currency;
    }

    public function setCurrency(bool $currency): void
    {
        $this->currency = $currency;
    }
}
