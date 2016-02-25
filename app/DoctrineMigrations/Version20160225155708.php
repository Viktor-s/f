<?php

namespace Furniture\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160225155708 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE product_pdp_intellectual_composite_expression_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_pdp_intellectual_element_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_pdp_intellectual_root_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE product_pdp_intellectual_composite_expression (id INT NOT NULL, parent_id INT DEFAULT NULL, type SMALLINT NOT NULL, prepend_text TEXT DEFAULT NULL, append_text TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E4418D3C727ACA70 ON product_pdp_intellectual_composite_expression (parent_id)');
        $this->addSql('CREATE TABLE product_pdp_intellectual_element (id INT NOT NULL, expression_id INT NOT NULL, prepend_text TEXT DEFAULT NULL, append_text TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_983028C6ADBB65A1 ON product_pdp_intellectual_element (expression_id)');
        $this->addSql('CREATE TABLE product_pdp_intellectual_root (id INT NOT NULL, expression_id INT NOT NULL, product_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DD67CD54ADBB65A1 ON product_pdp_intellectual_root (expression_id)');
        $this->addSql('CREATE INDEX IDX_DD67CD544584665A ON product_pdp_intellectual_root (product_id)');
        $this->addSql('ALTER TABLE product_pdp_intellectual_composite_expression ADD CONSTRAINT FK_E4418D3C727ACA70 FOREIGN KEY (parent_id) REFERENCES product_pdp_intellectual_composite_expression (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_pdp_intellectual_element ADD CONSTRAINT FK_983028C6ADBB65A1 FOREIGN KEY (expression_id) REFERENCES product_pdp_intellectual_composite_expression (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_pdp_intellectual_root ADD CONSTRAINT FK_DD67CD54ADBB65A1 FOREIGN KEY (expression_id) REFERENCES product_pdp_intellectual_composite_expression (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_pdp_intellectual_root ADD CONSTRAINT FK_DD67CD544584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE product_pdp_intellectual_composite_expression DROP CONSTRAINT FK_E4418D3C727ACA70');
        $this->addSql('ALTER TABLE product_pdp_intellectual_element DROP CONSTRAINT FK_983028C6ADBB65A1');
        $this->addSql('ALTER TABLE product_pdp_intellectual_root DROP CONSTRAINT FK_DD67CD54ADBB65A1');
        $this->addSql('DROP SEQUENCE product_pdp_intellectual_composite_expression_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_pdp_intellectual_element_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_pdp_intellectual_root_id_seq CASCADE');
        $this->addSql('DROP TABLE product_pdp_intellectual_composite_expression');
        $this->addSql('DROP TABLE product_pdp_intellectual_element');
        $this->addSql('DROP TABLE product_pdp_intellectual_root');
    }
}
