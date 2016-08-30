<?php

namespace Furniture\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160528175413 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE product_pdp_input ADD inteligent_pdp_composite_expression INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_pdp_input ADD CONSTRAINT FK_231160741FA21F68 FOREIGN KEY (inteligent_pdp_composite_expression) REFERENCES product_pdp_intellectual_composite_expression (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_231160741FA21F68 ON product_pdp_input (inteligent_pdp_composite_expression)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE product_pdp_input DROP CONSTRAINT FK_231160741FA21F68');
        $this->addSql('DROP INDEX UNIQ_231160741FA21F68');
        $this->addSql('ALTER TABLE product_pdp_input DROP inteligent_pdp_composite_expression');
    }
}
