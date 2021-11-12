<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112002449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        $this->addSql('CREATE SEQUENCE eur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE eur (id INT NOT NULL, 
                                             contractor_id INT NOT NULL, 
                                             date DATE NOT NULL, 
                                             amount DOUBLE PRECISION NOT NULL, 
                                             balance DOUBLE PRECISION NOT NULL, 
                                             description VARCHAR(255) DEFAULT NULL, 
                                             is_consistent BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_eur_contractor_id ON eur (contractor_id)');
        $this->addSql('ALTER TABLE eur ADD CONSTRAINT FK_eur_contractor_id 
                            FOREIGN KEY (contractor_id) REFERENCES contractor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE eur_id_seq CASCADE');
        $this->addSql('DROP TABLE eur');
    }
}
