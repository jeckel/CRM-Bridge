<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240205181501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mail (mail_id INT NOT NULL, contact_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', message_id VARCHAR(255) NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', subject VARCHAR(255) NOT NULL, from_name VARCHAR(255) NOT NULL, from_address VARCHAR(255) NOT NULL, to_string VARCHAR(255) NOT NULL, text_plain LONGTEXT NOT NULL, text_html LONGTEXT NOT NULL, INDEX IDX_5126AC48E7A1254A (contact_id), PRIMARY KEY(mail_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mail ADD CONSTRAINT FK_5126AC48E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (contact_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mail DROP FOREIGN KEY FK_5126AC48E7A1254A');
        $this->addSql('DROP TABLE mail');
    }
}
