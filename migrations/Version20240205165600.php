<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240205165600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE mail (message_id VARCHAR(255) NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', subject VARCHAR(255) NOT NULL, from_name VARCHAR(255) NOT NULL, from_address VARCHAR(255) NOT NULL, to_string VARCHAR(255) NOT NULL, text_plain LONGTEXT NOT NULL, text_html LONGTEXT NOT NULL, PRIMARY KEY(message_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact CHANGE email email VARCHAR(180) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4C62E638D5499347 ON contact (display_name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE mail');
        $this->addSql('DROP INDEX UNIQ_4C62E638D5499347 ON contact');
        $this->addSql('ALTER TABLE contact CHANGE email email VARCHAR(180) NOT NULL');
    }
}
