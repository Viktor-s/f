<?php

namespace Furniture\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160907195917 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE furniture_product_best_sellers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE furniture_product_best_sellers_set_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE furniture_product_best_sellers (id INT NOT NULL, product_id INT NOT NULL, best_seller_id INT NOT NULL, position INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C72ABF84584665A ON furniture_product_best_sellers (product_id)');
        $this->addSql('CREATE INDEX IDX_5C72ABF89105C0C9 ON furniture_product_best_sellers (best_seller_id)');
        $this->addSql('CREATE TABLE furniture_product_best_sellers_set (id INT NOT NULL, name VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE furniture_product_best_sellers ADD CONSTRAINT FK_5C72ABF84584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE furniture_product_best_sellers ADD CONSTRAINT FK_5C72ABF89105C0C9 FOREIGN KEY (best_seller_id) REFERENCES furniture_product_best_sellers_set (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE furniture_product_best_sellers DROP CONSTRAINT FK_5C72ABF89105C0C9');
        $this->addSql('DROP SEQUENCE furniture_product_best_sellers_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE furniture_product_best_sellers_set_id_seq CASCADE');
        $this->addSql('DROP TABLE furniture_product_best_sellers');
        $this->addSql('DROP TABLE furniture_product_best_sellers_set');
    }
}
