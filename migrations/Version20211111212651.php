<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211111212651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE chf_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE chf (
                                            id INT NOT NULL, 
                                            contractor_id INT NOT NULL, 
                                            date DATE NOT NULL, 
                                            amount DOUBLE PRECISION NOT NULL, 
                                            balance DOUBLE PRECISION NOT NULL, 
                                            description VARCHAR(255) DEFAULT NULL, 
                                            is_consistent BOOLEAN DEFAULT NULL, 
                                            PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_chf_contractor_id ON chf (contractor_id)');
        $this->addSql('ALTER TABLE chf ADD CONSTRAINT FK_chf_contractor_id
                               FOREIGN KEY (contractor_id) REFERENCES contractor (id)
                               NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE chf');
    }
}
