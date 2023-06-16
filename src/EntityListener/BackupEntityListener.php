<?php

namespace App\EntityListener;

use App\Entity\Backup;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsEntityListener(event: Events::prePersist, entity: Backup::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Backup::class)]
class BackupEntityListener
{
    public function prePersist(Backup $backup, LifecycleEventArgs $event): void
    {
        $backup->computeShortDate();
    }

    public function preUpdate(Backup $backup, LifecycleEventArgs $event): void
    {
        $backup->computeShortDate();
    }
}
