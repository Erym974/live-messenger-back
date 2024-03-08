<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240308100059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610537A1329');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5BA0E79C3');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5BA0E79C3 FOREIGN KEY (last_message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F8A0E4E7F');
        $this->addSql('ALTER TABLE message CHANGE encrypted_content encrypted_content LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F8A0E4E7F FOREIGN KEY (reply_id) REFERENCES message (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610537A1329');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5BA0E79C3');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5BA0E79C3 FOREIGN KEY (last_message_id) REFERENCES message (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F8A0E4E7F');
        $this->addSql('ALTER TABLE message CHANGE encrypted_content encrypted_content LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F8A0E4E7F FOREIGN KEY (reply_id) REFERENCES message (id) ON UPDATE NO ACTION ON DELETE SET NULL');
    }
}
