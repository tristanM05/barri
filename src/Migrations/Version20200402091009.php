<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200402091009 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article CHANGE describing describing VARCHAR(255) DEFAULT NULL, CHANGE costprice costprice NUMERIC(10, 2) DEFAULT NULL, CHANGE specialprice specialprice NUMERIC(10, 2) DEFAULT NULL, CHANGE productiondate productiondate DATETIME DEFAULT NULL, CHANGE leftdate leftdate DATETIME DEFAULT NULL, CHANGE isvisible isvisible TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE salepoint CHANGE visible visible TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD lastaction DATETIME NOT NULL, DROP isconnected, CHANGE activation_token activation_token VARCHAR(50) DEFAULT NULL, CHANGE reset_token reset_token VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article CHANGE describing describing VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE costprice costprice NUMERIC(10, 2) DEFAULT \'NULL\', CHANGE specialprice specialprice NUMERIC(10, 2) DEFAULT \'NULL\', CHANGE productiondate productiondate DATETIME DEFAULT \'NULL\', CHANGE leftdate leftdate DATETIME DEFAULT \'NULL\', CHANGE isvisible isvisible TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE client ADD isconnected TINYINT(1) NOT NULL, DROP lastaction, CHANGE activation_token activation_token VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE reset_token reset_token VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE salepoint CHANGE visible visible TINYINT(1) DEFAULT \'NULL\'');
    }
}
