<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251215005308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_category (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_category_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_1DAAB4872C2AC5D3 (translatable_id), UNIQUE INDEX product_category_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_subcategory (id INT AUTO_INCREMENT NOT NULL, product_category_id INT NOT NULL, INDEX IDX_A1F33A57BE6903FD (product_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_subcategory_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, locale VARCHAR(5) NOT NULL, INDEX IDX_6056F6BF2C2AC5D3 (translatable_id), UNIQUE INDEX product_subcategory_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_category_translation ADD CONSTRAINT FK_1DAAB4872C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES product_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_subcategory ADD CONSTRAINT FK_A1F33A57BE6903FD FOREIGN KEY (product_category_id) REFERENCES product_category (id)');
        $this->addSql('ALTER TABLE product_subcategory_translation ADD CONSTRAINT FK_6056F6BF2C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES product_subcategory (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD product_subcategory_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADEAF807B FOREIGN KEY (product_subcategory_id) REFERENCES product_subcategory (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADEAF807B ON product (product_subcategory_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADEAF807B');
        $this->addSql('ALTER TABLE product_category_translation DROP FOREIGN KEY FK_1DAAB4872C2AC5D3');
        $this->addSql('ALTER TABLE product_subcategory DROP FOREIGN KEY FK_A1F33A57BE6903FD');
        $this->addSql('ALTER TABLE product_subcategory_translation DROP FOREIGN KEY FK_6056F6BF2C2AC5D3');
        $this->addSql('DROP TABLE product_category');
        $this->addSql('DROP TABLE product_category_translation');
        $this->addSql('DROP TABLE product_subcategory');
        $this->addSql('DROP TABLE product_subcategory_translation');
        $this->addSql('DROP INDEX IDX_D34A04ADEAF807B ON product');
        $this->addSql('ALTER TABLE product DROP product_subcategory_id');
    }
}
