<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220629140917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE odpoved (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, otazka_id INTEGER NOT NULL, text VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_EB4987E53EC9665F ON odpoved (otazka_id)');
        $this->addSql('CREATE TABLE otazka (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, test_id INTEGER NOT NULL, text VARCHAR(255) NOT NULL, spravna_odpoved INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_2272C4611E5D0459 ON otazka (test_id)');
        $this->addSql('CREATE TABLE test (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nazev VARCHAR(255) NOT NULL, cas_vytvoreni DATETIME NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE odpoved');
        $this->addSql('DROP TABLE otazka');
        $this->addSql('DROP TABLE test');
    }
}
