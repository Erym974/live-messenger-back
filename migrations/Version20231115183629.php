<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231115183629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `group` ADD administrator_id INT NOT NULL');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C54B09E92C FOREIGN KEY (administrator_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6DC044C54B09E92C ON `group` (administrator_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C54B09E92C');
        $this->addSql('DROP INDEX IDX_6DC044C54B09E92C ON `group`');
        $this->addSql('ALTER TABLE `group` DROP administrator_id');
    }
}
