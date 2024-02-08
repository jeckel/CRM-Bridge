<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208092744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (company_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(180) NOT NULL, slug VARCHAR(180) NOT NULL, espo_company_id VARCHAR(30) DEFAULT NULL, UNIQUE INDEX UNIQ_4FBF094F989D9B62 (slug), PRIMARY KEY(company_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact ADD company_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638979B1AD6 FOREIGN KEY (company_id) REFERENCES company (company_id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_4C62E638979B1AD6 ON contact (company_id)');
        $this->addSql('ALTER TABLE mail DROP FOREIGN KEY FK_5126AC48E7A1254A');
        $this->addSql('ALTER TABLE mail ADD CONSTRAINT FK_5126AC48E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (contact_id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638979B1AD6');
        $this->addSql('DROP TABLE company');
        $this->addSql('ALTER TABLE mail DROP FOREIGN KEY FK_5126AC48E7A1254A');
        $this->addSql('ALTER TABLE mail ADD CONSTRAINT FK_5126AC48E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (contact_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP INDEX IDX_4C62E638979B1AD6 ON contact');
        $this->addSql('ALTER TABLE contact DROP company_id');
    }
}
