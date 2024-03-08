<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240308093905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friend DROP FOREIGN KEY FK_55EEAC619AC0396');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC619AC0396 FOREIGN KEY (conversation_id) REFERENCES `group` (id)');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5BA0E79C3');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5BA0E79C3 FOREIGN KEY (last_message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F8A0E4E7F');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE message CHANGE content encrypted_content LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F8A0E4E7F FOREIGN KEY (reply_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES `group` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friend DROP FOREIGN KEY FK_55EEAC619AC0396');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC619AC0396 FOREIGN KEY (conversation_id) REFERENCES `group` (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C5BA0E79C3');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C5BA0E79C3 FOREIGN KEY (last_message_id) REFERENCES message (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F8A0E4E7F');
        $this->addSql('ALTER TABLE message CHANGE encrypted_content content LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES `group` (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F8A0E4E7F FOREIGN KEY (reply_id) REFERENCES message (id) ON UPDATE NO ACTION ON DELETE SET NULL');
    }
}
