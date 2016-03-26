<?php

namespace Furniture\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160326221951 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        // Create extension require root user previlegious.
        // $this->addSql("CREATE EXTENSION unaccent;");
        $this->addSql("CREATE OR REPLACE FUNCTION product_translation_trigger() RETURNS trigger AS $$
                        DECLARE lang varchar;
                        BEGIN
                         SELECT lan INTO lang FROM (
                            SELECT
                                UNNEST(ARRAY['en_US','en_GB','es_ES','de_DE','it_IT']) as locale,
                                UNNEST(ARRAY['english','english','spanish','german','italian']) as lan)t
                            WHERE locale = new.locale;
                          new.search_tsv :=
                            setweight(to_tsvector(lang::regconfig, coalesce(new.name, '')), 'A')||
                            setweight(to_tsvector(lang::regconfig, coalesce(new.description, '')), 'C');

                          RETURN new;
                        END
                        $$ LANGUAGE plpgsql;");

        $this->addSql("CREATE TRIGGER productTranslationTsVectorUpdate BEFORE INSERT OR UPDATE
                        ON product_translation FOR EACH ROW EXECUTE PROCEDURE product_translation_trigger();");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql("DROP TRIGGER IF EXISTS productTranslationTsVectorUpdate ON product_translation;");
        $this->addSql("DROP FUNCTION IF EXISTS product_translation_trigger();");
        // Create extension require root user previlegious.
        // $this->addSql("DROP EXTENSION IF EXISTS unaccent;");
    }
}
