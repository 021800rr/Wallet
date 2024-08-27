<?php

namespace App\Tests\EntityListener;

use App\Entity\Backup;
use App\EntityListener\BackupEntityListener;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;

class BackupEntityListenerTest extends TestCase
{
    public function testPrePersist(): void
    {
        $backup = $this->createMock(Backup::class);
        $backup->expects($this->once())
            ->method('computeShortDate');

        $eventArgs = $this->createMock(LifecycleEventArgs::class);

        $listener = new BackupEntityListener();
        $listener->prePersist($backup, $eventArgs);
    }

    public function testPreUpdate(): void
    {
        $backup = $this->createMock(Backup::class);
        $backup->expects($this->once())
            ->method('computeShortDate');

        $eventArgs = $this->createMock(LifecycleEventArgs::class);

        $listener = new BackupEntityListener();
        $listener->preUpdate($backup, $eventArgs);
    }
}
