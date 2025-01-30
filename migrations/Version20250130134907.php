<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250130134907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reposts (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, original_post_id INT DEFAULT NULL, content LONGTEXT DEFAULT NULL, INDEX IDX_F0DDCD72A76ED395 (user_id), INDEX IDX_F0DDCD72CD09ADDB (original_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reposts ADD CONSTRAINT FK_F0DDCD72A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reposts ADD CONSTRAINT FK_F0DDCD72CD09ADDB FOREIGN KEY (original_post_id) REFERENCES reposts (id)');
        $this->addSql('ALTER TABLE posts ADD original_post_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFACD09ADDB FOREIGN KEY (original_post_id) REFERENCES reposts (id)');
        $this->addSql('CREATE INDEX IDX_885DBAFACD09ADDB ON posts (original_post_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFACD09ADDB');
        $this->addSql('ALTER TABLE reposts DROP FOREIGN KEY FK_F0DDCD72A76ED395');
        $this->addSql('ALTER TABLE reposts DROP FOREIGN KEY FK_F0DDCD72CD09ADDB');
        $this->addSql('DROP TABLE reposts');
        $this->addSql('DROP INDEX IDX_885DBAFACD09ADDB ON posts');
        $this->addSql('ALTER TABLE posts DROP original_post_id');
    }
}
