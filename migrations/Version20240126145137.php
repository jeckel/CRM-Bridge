<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240126145137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_activity (contact_activity_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', contact_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', subject VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_365195CDE7A1254A (contact_id), PRIMARY KEY(contact_activity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact_activity ADD CONSTRAINT FK_365195CDE7A1254A FOREIGN KEY (contact_id) REFERENCES contact (contact_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_activity DROP FOREIGN KEY FK_365195CDE7A1254A');
        $this->addSql('DROP TABLE contact_activity');
    }
}
