<?php

namespace Furniture\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160528183638 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE product_pdp_intellectual_root DROP CONSTRAINT FK_DD67CD54ADBB65A1');
        $this->addSql('ALTER TABLE product_pdp_intellectual_root ADD CONSTRAINT FK_DD67CD54ADBB65A1 FOREIGN KEY (expression_id) REFERENCES product_pdp_intellectual_composite_expression (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE product_pdp_intellectual_root DROP CONSTRAINT fk_dd67cd54adbb65a1');
        $this->addSql('ALTER TABLE product_pdp_intellectual_root ADD CONSTRAINT fk_dd67cd54adbb65a1 FOREIGN KEY (expression_id) REFERENCES product_pdp_intellectual_composite_expression (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
