<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260216002449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profile_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_A17403112C2AC5D3 (translatable_id), UNIQUE INDEX profile_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE profile_translation ADD CONSTRAINT FK_A17403112C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user_admin (id)');
        $this->addSql('ALTER TABLE profile ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD enabled TINYINT(1) NOT NULL, DROP slug_en, DROP description_en, DROP description_fr, DROP description_es, DROP slug_fr, DROP slug_es');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE profile_translation DROP FOREIGN KEY FK_A17403112C2AC5D3');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE profile_translation');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('ALTER TABLE profile ADD slug_en VARCHAR(255) NOT NULL, ADD description_en VARCHAR(255) NOT NULL, ADD description_fr VARCHAR(255) NOT NULL, ADD description_es VARCHAR(255) NOT NULL, ADD slug_fr VARCHAR(255) NOT NULL, ADD slug_es VARCHAR(255) NOT NULL, DROP updated_at, DROP enabled');
    }
}
