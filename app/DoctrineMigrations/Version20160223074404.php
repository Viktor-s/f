<?php

namespace Furniture\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160223074404 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04ADC7AF27D2');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADC7AF27D2 FOREIGN KEY (factory_id) REFERENCES factory (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE product_part_material DROP CONSTRAINT FK_3101EC18C7AF27D2');
        $this->addSql('ALTER TABLE product_part_material ADD CONSTRAINT FK_3101EC18C7AF27D2 FOREIGN KEY (factory_id) REFERENCES factory (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9C7AF27D2');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9C7AF27D2 FOREIGN KEY (factory_id) REFERENCES factory (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE product_part_material DROP CONSTRAINT fk_3101ec18c7af27d2');
        $this->addSql('ALTER TABLE product_part_material ADD CONSTRAINT fk_3101ec18c7af27d2 FOREIGN KEY (factory_id) REFERENCES factory (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE product DROP CONSTRAINT fk_d34a04adc7af27d2');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT fk_d34a04adc7af27d2 FOREIGN KEY (factory_id) REFERENCES factory (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE users DROP CONSTRAINT fk_1483a5e9c7af27d2');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT fk_1483a5e9c7af27d2 FOREIGN KEY (factory_id) REFERENCES factory (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
