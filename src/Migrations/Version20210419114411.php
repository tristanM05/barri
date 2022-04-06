<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210419114411 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE decaissement ADD journal_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE decaissement ADD CONSTRAINT FK_E5CCA29B478E8802 FOREIGN KEY (journal_id) REFERENCES journal (id)');
        $this->addSql('CREATE INDEX IDX_E5CCA29B478E8802 ON decaissement (journal_id)');
        $this->addSql('ALTER TABLE encaissement ADD journal_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE encaissement ADD CONSTRAINT FK_5D4869B0478E8802 FOREIGN KEY (journal_id) REFERENCES journal (id)');
        $this->addSql('CREATE INDEX IDX_5D4869B0478E8802 ON encaissement (journal_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE decaissement DROP FOREIGN KEY FK_E5CCA29B478E8802');
        $this->addSql('DROP INDEX IDX_E5CCA29B478E8802 ON decaissement');
        $this->addSql('ALTER TABLE decaissement DROP journal_id');
        $this->addSql('ALTER TABLE encaissement DROP FOREIGN KEY FK_5D4869B0478E8802');
        $this->addSql('DROP INDEX IDX_5D4869B0478E8802 ON encaissement');
        $this->addSql('ALTER TABLE encaissement DROP journal_id');
    }
}
