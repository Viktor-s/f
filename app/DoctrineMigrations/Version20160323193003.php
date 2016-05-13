<?php

namespace Furniture\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160323193003 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE posts DROP CONSTRAINT fk_885dbafaf3f91591');
        $this->addSql('DROP INDEX idx_885dbafaf3f91591');
        $this->addSql('ALTER TABLE posts RENAME COLUMN factry_id TO factory_id');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAC7AF27D2 FOREIGN KEY (factory_id) REFERENCES factory (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_885DBAFAC7AF27D2 ON posts (factory_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE posts DROP CONSTRAINT FK_885DBAFAC7AF27D2');
        $this->addSql('DROP INDEX IDX_885DBAFAC7AF27D2');
        $this->addSql('ALTER TABLE posts RENAME COLUMN factory_id TO factry_id');
        $this->addSql('ALTER TABLE posts ADD CONSTRAINT fk_885dbafaf3f91591 FOREIGN KEY (factry_id) REFERENCES factory (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_885dbafaf3f91591 ON posts (factry_id)');
    }
}
