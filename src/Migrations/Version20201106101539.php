<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201106101539 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE graph_value (id INT AUTO_INCREMENT NOT NULL, id_client_id INT NOT NULL, date DATE NOT NULL, value_stock VARCHAR(20) NOT NULL, nd_stock VARCHAR(20) NOT NULL, INDEX IDX_9948740C99DED506 (id_client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE graph_value ADD CONSTRAINT FK_9948740C99DED506 FOREIGN KEY (id_client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE article CHANGE productiondate productiondate DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE salepoint CHANGE visible visible TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_configuration CHANGE maxsupertop maxsupertop INT DEFAULT NULL, CHANGE maxtop maxtop INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE graph_value');
        $this->addSql('ALTER TABLE article CHANGE productiondate productiondate DATETIME NOT NULL');
        $this->addSql('ALTER TABLE salepoint CHANGE visible visible TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user_configuration CHANGE maxsupertop maxsupertop INT NOT NULL, CHANGE maxtop maxtop INT NOT NULL');
    }
}
