<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251108145707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, name, email, password, created_at, updated_at, email_verified, email_verification_token FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id VARCHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , email_verified BOOLEAN NOT NULL, email_verification_token VARCHAR(64) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO users (id, name, email, password, created_at, updated_at, email_verified, email_verification_token) SELECT id, name, email, password, created_at, updated_at, email_verified, email_verification_token FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, name, email, password, created_at, updated_at, email_verified, email_verification_token FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id VARCHAR(36) NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , email_verified BOOLEAN DEFAULT 0 NOT NULL, email_verification_token VARCHAR(64) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO users (id, name, email, password, created_at, updated_at, email_verified, email_verification_token) SELECT id, name, email, password, created_at, updated_at, email_verified, email_verification_token FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }
}
