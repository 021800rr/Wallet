CREATE SEQUENCE chf_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE chf (
        id INT NOT NULL,
        contractor_id INT NOT NULL,
        date DATE NOT NULL,
        amount DOUBLE PRECISION NOT NULL,
        balance DOUBLE PRECISION NOT NULL,
        description VARCHAR(255) DEFAULT NULL,
        is_consistent BOOLEAN DEFAULT NULL,
        PRIMARY KEY(id));
CREATE INDEX IDX_chf_contractor_id ON chf (contractor_id);
ALTER TABLE chf ADD CONSTRAINT FK_chf_contractor_id
       FOREIGN KEY (contractor_id) REFERENCES contractor (id)
       NOT DEFERRABLE INITIALLY IMMEDIATE;

CREATE SEQUENCE eur_id_seq INCREMENT BY 1 MINVALUE 1 START 1;
CREATE TABLE eur (id INT NOT NULL,
         contractor_id INT NOT NULL,
         date DATE NOT NULL,
         amount DOUBLE PRECISION NOT NULL,
         balance DOUBLE PRECISION NOT NULL,
         description VARCHAR(255) DEFAULT NULL,
         is_consistent BOOLEAN DEFAULT NULL, PRIMARY KEY(id));
CREATE INDEX IDX_eur_contractor_id ON eur (contractor_id);
ALTER TABLE eur ADD CONSTRAINT FK_eur_contractor_id
        FOREIGN KEY (contractor_id) REFERENCES contractor (id)
        NOT DEFERRABLE INITIALLY IMMEDIATE;
