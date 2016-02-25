<?php

namespace Furniture\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160224123317 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE furniture_product_part DROP CONSTRAINT FK_5D3DADA1C8B459F3');
        $this->addSql('ALTER TABLE furniture_product_part ADD CONSTRAINT FK_5D3DADA1C8B459F3 FOREIGN KEY (product_part_type_id) REFERENCES furniture_product_part_type (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE product_part_material_option_value DROP CONSTRAINT FK_9BE38AEDBD4137EF');
        $this->addSql('ALTER TABLE product_part_material_option_value ADD CONSTRAINT FK_9BE38AEDBD4137EF FOREIGN KEY (product_extension_option_id) REFERENCES product_part_material_option (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE product_part_material_option_value DROP CONSTRAINT fk_9be38aedbd4137ef');
        $this->addSql('ALTER TABLE product_part_material_option_value ADD CONSTRAINT fk_9be38aedbd4137ef FOREIGN KEY (product_extension_option_id) REFERENCES product_part_material_option (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE furniture_product_part DROP CONSTRAINT fk_5d3dada1c8b459f3');
        $this->addSql('ALTER TABLE furniture_product_part ADD CONSTRAINT fk_5d3dada1c8b459f3 FOREIGN KEY (product_part_type_id) REFERENCES furniture_product_part_type (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
