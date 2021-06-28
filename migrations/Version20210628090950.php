<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210628090950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE backup ADD year_month VARCHAR(7)');
        $this->addSql("UPDATE backup SET year_month=substr(date::character varying(255),1, 7)");
        $this->addSql('ALTER TABLE backup ALTER COLUMN year_month SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE backup DROP year_month');
    }
}
