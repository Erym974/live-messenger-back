<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240308103247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD profile_picture_id INT NOT NULL, ADD cover_picture_id INT NOT NULL, DROP profile_picture, DROP cover_picture');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649292E8AE2 FOREIGN KEY (profile_picture_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C50D86A0 FOREIGN KEY (cover_picture_id) REFERENCES file (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649292E8AE2 ON user (profile_picture_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649C50D86A0 ON user (cover_picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649292E8AE2');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C50D86A0');
        $this->addSql('DROP INDEX IDX_8D93D649292E8AE2 ON user');
        $this->addSql('DROP INDEX IDX_8D93D649C50D86A0 ON user');
        $this->addSql('ALTER TABLE user ADD profile_picture LONGTEXT NOT NULL, ADD cover_picture LONGTEXT NOT NULL, DROP profile_picture_id, DROP cover_picture_id');
    }
}
