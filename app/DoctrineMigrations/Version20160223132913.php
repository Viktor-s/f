<?php

namespace Furniture\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160223132913 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE product_categories DROP CONSTRAINT FK_A994194312469DE2');
        $this->addSql('ALTER TABLE product_categories ADD CONSTRAINT FK_A994194312469DE2 FOREIGN KEY (category_id) REFERENCES product_category (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE product_spaces DROP CONSTRAINT FK_BF5725DC23575340');
        $this->addSql('ALTER TABLE product_spaces ADD CONSTRAINT FK_BF5725DC23575340 FOREIGN KEY (space_id) REFERENCES product_space (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE product_types DROP CONSTRAINT FK_F86CF26CC54C8C93');
        $this->addSql('ALTER TABLE product_types ADD CONSTRAINT FK_F86CF26CC54C8C93 FOREIGN KEY (type_id) REFERENCES product_type (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE product_styles DROP CONSTRAINT FK_D426BB51BACD6074');
        $this->addSql('ALTER TABLE product_styles ADD CONSTRAINT FK_D426BB51BACD6074 FOREIGN KEY (style_id) REFERENCES product_style (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('ALTER TABLE product_readiness_relation DROP CONSTRAINT FK_37C0939FBF61C716');
        $this->addSql('ALTER TABLE product_readiness_relation ADD CONSTRAINT FK_37C0939FBF61C716 FOREIGN KEY (readiness_id) REFERENCES product_readiness (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE product_categories DROP CONSTRAINT fk_a994194312469de2');
        $this->addSql('ALTER TABLE product_categories ADD CONSTRAINT fk_a994194312469de2 FOREIGN KEY (category_id) REFERENCES product_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_spaces DROP CONSTRAINT fk_bf5725dc23575340');
        $this->addSql('ALTER TABLE product_spaces ADD CONSTRAINT fk_bf5725dc23575340 FOREIGN KEY (space_id) REFERENCES product_space (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_types DROP CONSTRAINT fk_f86cf26cc54c8c93');
        $this->addSql('ALTER TABLE product_types ADD CONSTRAINT fk_f86cf26cc54c8c93 FOREIGN KEY (type_id) REFERENCES product_type (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_styles DROP CONSTRAINT fk_d426bb51bacd6074');
        $this->addSql('ALTER TABLE product_styles ADD CONSTRAINT fk_d426bb51bacd6074 FOREIGN KEY (style_id) REFERENCES product_style (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_readiness_relation DROP CONSTRAINT fk_37c0939fbf61c716');
        $this->addSql('ALTER TABLE product_readiness_relation ADD CONSTRAINT fk_37c0939fbf61c716 FOREIGN KEY (readiness_id) REFERENCES product_readiness (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
