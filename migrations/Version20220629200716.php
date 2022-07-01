<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220629200716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE uzivatel (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, jmeno VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1C0F667EB4838404 ON uzivatel (jmeno)');
        $this->addSql('DROP INDEX IDX_EB4987E53EC9665F');
        $this->addSql('CREATE TEMPORARY TABLE __temp__odpoved AS SELECT id, otazka_id, text FROM odpoved');
        $this->addSql('DROP TABLE odpoved');
        $this->addSql('CREATE TABLE odpoved (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, otazka_id INTEGER NOT NULL, text VARCHAR(255) NOT NULL, CONSTRAINT FK_EB4987E53EC9665F FOREIGN KEY (otazka_id) REFERENCES otazka (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO odpoved (id, otazka_id, text) SELECT id, otazka_id, text FROM __temp__odpoved');
        $this->addSql('DROP TABLE __temp__odpoved');
        $this->addSql('CREATE INDEX IDX_EB4987E53EC9665F ON odpoved (otazka_id)');
        $this->addSql('DROP INDEX IDX_2272C4611E5D0459');
        $this->addSql('CREATE TEMPORARY TABLE __temp__otazka AS SELECT id, test_id, text, spravna_odpoved FROM otazka');
        $this->addSql('DROP TABLE otazka');
        $this->addSql('CREATE TABLE otazka (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, test_id INTEGER NOT NULL, text VARCHAR(255) NOT NULL, spravna_odpoved INTEGER NOT NULL, CONSTRAINT FK_2272C4611E5D0459 FOREIGN KEY (test_id) REFERENCES test (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO otazka (id, test_id, text, spravna_odpoved) SELECT id, test_id, text, spravna_odpoved FROM __temp__otazka');
        $this->addSql('DROP TABLE __temp__otazka');
        $this->addSql('CREATE INDEX IDX_2272C4611E5D0459 ON otazka (test_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE uzivatel');
        $this->addSql('DROP INDEX IDX_EB4987E53EC9665F');
        $this->addSql('CREATE TEMPORARY TABLE __temp__odpoved AS SELECT id, otazka_id, text FROM odpoved');
        $this->addSql('DROP TABLE odpoved');
        $this->addSql('CREATE TABLE odpoved (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, otazka_id INTEGER NOT NULL, text VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO odpoved (id, otazka_id, text) SELECT id, otazka_id, text FROM __temp__odpoved');
        $this->addSql('DROP TABLE __temp__odpoved');
        $this->addSql('CREATE INDEX IDX_EB4987E53EC9665F ON odpoved (otazka_id)');
        $this->addSql('DROP INDEX IDX_2272C4611E5D0459');
        $this->addSql('CREATE TEMPORARY TABLE __temp__otazka AS SELECT id, test_id, text, spravna_odpoved FROM otazka');
        $this->addSql('DROP TABLE otazka');
        $this->addSql('CREATE TABLE otazka (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, test_id INTEGER NOT NULL, text VARCHAR(255) NOT NULL, spravna_odpoved INTEGER NOT NULL)');
        $this->addSql('INSERT INTO otazka (id, test_id, text, spravna_odpoved) SELECT id, test_id, text, spravna_odpoved FROM __temp__otazka');
        $this->addSql('DROP TABLE __temp__otazka');
        $this->addSql('CREATE INDEX IDX_2272C4611E5D0459 ON otazka (test_id)');
    }
}
