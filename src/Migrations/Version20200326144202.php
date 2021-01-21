<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200326144202 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, salepoint_id INT NOT NULL, product_status_id INT NOT NULL, subfamily_id INT NOT NULL, number INT NOT NULL, designation VARCHAR(100) NOT NULL, describing VARCHAR(255) DEFAULT NULL, costprice NUMERIC(10, 2) DEFAULT NULL, referenceprice NUMERIC(10, 2) NOT NULL, specialprice NUMERIC(10, 2) DEFAULT NULL, productiondate DATE DEFAULT NULL, leftdate DATE DEFAULT NULL, isvisible TINYINT(1) DEFAULT NULL, INDEX IDX_23A0E6619EB6921 (client_id), INDEX IDX_23A0E66C17EED4D (salepoint_id), INDEX IDX_23A0E66557B630 (product_status_id), INDEX IDX_23A0E665731FD53 (subfamily_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6619EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66C17EED4D FOREIGN KEY (salepoint_id) REFERENCES salepoint (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66557B630 FOREIGN KEY (product_status_id) REFERENCES product_status (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E665731FD53 FOREIGN KEY (subfamily_id) REFERENCES subfamily (id)');
        $this->addSql('ALTER TABLE salepoint CHANGE visible visible TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE client CHANGE isadmin isadmin TINYINT(1) DEFAULT NULL, CHANGE activation_token activation_token VARCHAR(50) DEFAULT NULL, CHANGE reset_token reset_token VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE article');
        $this->addSql('ALTER TABLE client CHANGE isadmin isadmin TINYINT(1) DEFAULT \'NULL\', CHANGE activation_token activation_token VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE reset_token reset_token VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE salepoint CHANGE visible visible TINYINT(1) DEFAULT \'NULL\'');
    }
}
