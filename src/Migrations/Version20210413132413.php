<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210413132413 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE encaissement_payment_mode DROP FOREIGN KEY FK_DE9BD9856EAC8BA0');
        $this->addSql('DROP TABLE encaissement_payment_mode');
        $this->addSql('DROP TABLE payment_mode');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE encaissement_payment_mode (encaissement_id INT NOT NULL, payment_mode_id INT NOT NULL, INDEX IDX_DE9BD9856EAC8BA0 (payment_mode_id), INDEX IDX_DE9BD9856F272231 (encaissement_id), PRIMARY KEY(encaissement_id, payment_mode_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE payment_mode (id INT AUTO_INCREMENT NOT NULL, cb NUMERIC(10, 2) DEFAULT NULL, esp NUMERIC(10, 2) DEFAULT NULL, chq NUMERIC(10, 2) DEFAULT NULL, other NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE encaissement_payment_mode ADD CONSTRAINT FK_DE9BD9856EAC8BA0 FOREIGN KEY (payment_mode_id) REFERENCES payment_mode (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE encaissement_payment_mode ADD CONSTRAINT FK_DE9BD9856F272231 FOREIGN KEY (encaissement_id) REFERENCES encaissement (id) ON DELETE CASCADE');
    }
}
