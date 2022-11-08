<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221108111441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE public.backup ALTER COLUMN date SET DATA TYPE DATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE public.backup ALTER COLUMN date SET DATA TYPE timestamp(0) without time zone');
    }
}
