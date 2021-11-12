<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210607122943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE backup_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE contractor_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE fee_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE wallet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql('CREATE TABLE backup (
                                            id INT NOT NULL, 
                                            contractor_id INT NOT NULL, 
                                            date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                                            amount DOUBLE PRECISION NOT NULL, 
                                            retiring DOUBLE PRECISION NOT NULL, 
                                            holiday DOUBLE PRECISION NOT NULL, 
                                            balance DOUBLE PRECISION NOT NULL, 
                                            description VARCHAR(255) DEFAULT NULL, 
                                            is_consistent BOOLEAN DEFAULT NULL, 
                                            PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_backup_contractor_id ON backup (contractor_id)');

        $this->addSql('CREATE TABLE contractor (
                                            id INT NOT NULL, 
                                            description VARCHAR(255) NOT NULL, 
                                            account VARCHAR(255) DEFAULT NULL, 
                                            PRIMARY KEY(id))');

        $this->addSql('CREATE TABLE fee (id INT NOT NULL, 
                                            contractor_id INT NOT NULL, 
                                            date INT NOT NULL, 
                                            amount DOUBLE PRECISION NOT NULL, 
                                            PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_fee_contractor_id ON fee (contractor_id)');

        $this->addSql('CREATE TABLE wallet (
                                            id INT NOT NULL, 
                                            contractor_id INT NOT NULL, 
                                            date DATE NOT NULL, 
                                            amount DOUBLE PRECISION NOT NULL, 
                                            balance DOUBLE PRECISION NOT NULL, 
                                            description VARCHAR(255) DEFAULT NULL, 
                                            is_consistent BOOLEAN DEFAULT NULL, 
                                            PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_wallet_contractor_id ON wallet (contractor_id)');

        $this->addSql('ALTER TABLE backup ADD CONSTRAINT FK_backup_contractor_id 
                               FOREIGN KEY (contractor_id) REFERENCES contractor (id)
                               NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE fee ADD CONSTRAINT FK_fee_contractor_id 
                               FOREIGN KEY (contractor_id) REFERENCES contractor (id)
                               NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_wallet_contractor_id
                               FOREIGN KEY (contractor_id) REFERENCES contractor (id)
                               NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE backup DROP CONSTRAINT FK_backup_contractor_id');
        $this->addSql('ALTER TABLE fee DROP CONSTRAINT FK_fee_contractor_id');
        $this->addSql('ALTER TABLE wallet DROP CONSTRAINT FK_wallet_contractor_id');
        $this->addSql('DROP SEQUENCE backup_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE contractor_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE fee_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE wallet_id_seq CASCADE');
        $this->addSql('DROP TABLE backup');
        $this->addSql('DROP TABLE contractor');
        $this->addSql('DROP TABLE fee');
        $this->addSql('DROP TABLE wallet');
    }
}
