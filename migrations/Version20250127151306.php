<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127151306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE posts_users (posts_id INT NOT NULL, users_id INT NOT NULL, INDEX IDX_A684FEF8D5E258C5 (posts_id), INDEX IDX_A684FEF867B3B43D (users_id), PRIMARY KEY(posts_id, users_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE posts_users ADD CONSTRAINT FK_A684FEF8D5E258C5 FOREIGN KEY (posts_id) REFERENCES posts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE posts_users ADD CONSTRAINT FK_A684FEF867B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts_users DROP FOREIGN KEY FK_A684FEF8D5E258C5');
        $this->addSql('ALTER TABLE posts_users DROP FOREIGN KEY FK_A684FEF867B3B43D');
        $this->addSql('DROP TABLE posts_users');
    }
}
