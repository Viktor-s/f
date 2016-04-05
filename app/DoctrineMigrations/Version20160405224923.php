<?php

namespace Furniture\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160405224923 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE product_readiness_relation DROP CONSTRAINT fk_37c0939fbf61c716');
        $this->addSql('DROP SEQUENCE product_readiness_id_seq CASCADE');
        $this->addSql('DROP TABLE product_readiness');
        $this->addSql('DROP TABLE product_readiness_relation');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE product_readiness_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE product_readiness (id INT NOT NULL, name VARCHAR(255) NOT NULL, "position" INT DEFAULT 0, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE product_readiness_relation (product_id INT NOT NULL, readiness_id INT NOT NULL, PRIMARY KEY(product_id, readiness_id))');
        $this->addSql('CREATE INDEX idx_37c0939fbf61c716 ON product_readiness_relation (readiness_id)');
        $this->addSql('CREATE INDEX idx_37c0939f4584665a ON product_readiness_relation (product_id)');
        $this->addSql('ALTER TABLE product_readiness_relation ADD CONSTRAINT fk_37c0939f4584665a FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_readiness_relation ADD CONSTRAINT fk_37c0939fbf61c716 FOREIGN KEY (readiness_id) REFERENCES product_readiness (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
