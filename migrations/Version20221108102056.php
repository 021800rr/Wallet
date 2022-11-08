<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221108102056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE backup RENAME COLUMN is_consistent TO interest');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE backup RENAME COLUMN interest TO is_consistent');
    }
}
