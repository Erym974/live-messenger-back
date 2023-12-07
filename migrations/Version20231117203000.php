<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231117203000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friend ADD conversation_id INT NOT NULL');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC619AC0396 FOREIGN KEY (conversation_id) REFERENCES `group` (id)');
        $this->addSql('CREATE INDEX IDX_55EEAC619AC0396 ON friend (conversation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friend DROP FOREIGN KEY FK_55EEAC619AC0396');
        $this->addSql('DROP INDEX IDX_55EEAC619AC0396 ON friend');
        $this->addSql('ALTER TABLE friend DROP conversation_id');
    }
}
