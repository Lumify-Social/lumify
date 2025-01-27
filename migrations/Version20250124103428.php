<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250124103428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE profilpage');
        $this->addSql('ALTER TABLE posts ADD likes INT NOT NULL');
        $this->addSql('ALTER TABLE users ADD password_hash VARCHAR(255) NOT NULL, DROP password, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE roles roles JSON DEFAULT \'[]\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profilpage (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, bio VARCHAR(500) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, profile_picture LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE users ADD password VARCHAR(180) NOT NULL, DROP password_hash, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE posts DROP likes');
    }
}
