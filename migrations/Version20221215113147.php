<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221215113147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE refresh_token (
                              id INT NOT NULL, 
                              refresh_token VARCHAR(250) NOT NULL, 
                              username  VARCHAR(250) NOT NULL, 
                              valid TIMESTAMP(0) WITH TIME ZONE,
                              PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX PK_refresh_token_id ON refresh_token (id)');
        $this->addSql('CREATE SEQUENCE refresh_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE refresh_token_id_seq CASCADE');
        $this->addSql('DROP TABLE refresh_token');
    }
}
