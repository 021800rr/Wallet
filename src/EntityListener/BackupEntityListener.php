<?php

namespace App\EntityListener;

use App\Entity\Backup;
use Doctrine\ORM\Event\LifecycleEventArgs;

class BackupEntityListener
{
    public function prePersist(Backup $backup, LifecycleEventArgs $event)
    {
        $backup->computeShortDate();
    }

    public function preUpdate(Backup $backup, LifecycleEventArgs $event)
    {
        $backup->computeShortDate();
    }
}
