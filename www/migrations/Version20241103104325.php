<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241103104325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album ADD cover_id INT DEFAULT NULL, CHANGE date_taken date_taken DATE NOT NULL');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43922726E9 FOREIGN KEY (cover_id) REFERENCES photo (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_39986E43922726E9 ON album (cover_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43922726E9');
        $this->addSql('DROP INDEX UNIQ_39986E43922726E9 ON album');
        $this->addSql('ALTER TABLE album DROP cover_id, CHANGE date_taken date_taken DATE DEFAULT NULL');
    }
}
