<?php

namespace Furniture\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160830231302 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE related_products (product_id INT NOT NULL, related_product_id INT NOT NULL, PRIMARY KEY(product_id, related_product_id))');
        $this->addSql('CREATE INDEX IDX_153914F74584665A ON related_products (product_id)');
        $this->addSql('CREATE INDEX IDX_153914F7CF496EEA ON related_products (related_product_id)');
        $this->addSql('ALTER TABLE related_products ADD CONSTRAINT FK_153914F74584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE related_products ADD CONSTRAINT FK_153914F7CF496EEA FOREIGN KEY (related_product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE related_products');
    }
}
