<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240308140938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5BA0E79C3');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5BA0E79C3 FOREIGN KEY (last_message_id) REFERENCES message (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5BA0E79C3');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5BA0E79C3 FOREIGN KEY (last_message_id) REFERENCES message (id)');
    }
}
