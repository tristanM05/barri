<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210413130909 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE decaissement ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE decaissement ADD CONSTRAINT FK_E5CCA29BA76ED395 FOREIGN KEY (user_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_E5CCA29BA76ED395 ON decaissement (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE decaissement DROP FOREIGN KEY FK_E5CCA29BA76ED395');
        $this->addSql('DROP INDEX IDX_E5CCA29BA76ED395 ON decaissement');
        $this->addSql('ALTER TABLE decaissement DROP user_id');
    }
}
