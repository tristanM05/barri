<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210414122039 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE encaissement (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ventes ADD encaissement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ventes ADD CONSTRAINT FK_64EC489A6F272231 FOREIGN KEY (encaissement_id) REFERENCES encaissement (id)');
        $this->addSql('CREATE INDEX IDX_64EC489A6F272231 ON ventes (encaissement_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ventes DROP FOREIGN KEY FK_64EC489A6F272231');
        $this->addSql('DROP TABLE encaissement');
        $this->addSql('DROP INDEX IDX_64EC489A6F272231 ON ventes');
        $this->addSql('ALTER TABLE ventes DROP encaissement_id');
    }
}
