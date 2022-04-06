<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210414121909 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE encaissement_article DROP FOREIGN KEY FK_9D22B3F16F272231');
        $this->addSql('DROP TABLE encaissement');
        $this->addSql('DROP TABLE encaissement_article');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE encaissement (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, date DATETIME NOT NULL, amout NUMERIC(10, 2) NOT NULL, comment LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, cb NUMERIC(10, 2) DEFAULT NULL, esp NUMERIC(10, 2) DEFAULT NULL, chq NUMERIC(10, 2) DEFAULT NULL, other NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_5D4869B019EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE encaissement_article (encaissement_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_9D22B3F17294869C (article_id), INDEX IDX_9D22B3F16F272231 (encaissement_id), PRIMARY KEY(encaissement_id, article_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE encaissement ADD CONSTRAINT FK_5D4869B019EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE encaissement_article ADD CONSTRAINT FK_9D22B3F16F272231 FOREIGN KEY (encaissement_id) REFERENCES encaissement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE encaissement_article ADD CONSTRAINT FK_9D22B3F17294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
    }
}
