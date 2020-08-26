<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200825111247 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tag_topic');
        $this->addSql('ALTER TABLE post ADD accepted BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE tag_topic (tag_id INT NOT NULL, topic_id INT NOT NULL, PRIMARY KEY(tag_id, topic_id))');
        $this->addSql('CREATE INDEX idx_bfacc71dbad26311 ON tag_topic (tag_id)');
        $this->addSql('CREATE INDEX idx_bfacc71d1f55203d ON tag_topic (topic_id)');
        $this->addSql('ALTER TABLE tag_topic ADD CONSTRAINT fk_bfacc71dbad26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_topic ADD CONSTRAINT fk_bfacc71d1f55203d FOREIGN KEY (topic_id) REFERENCES topic (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post DROP accepted');
    }
}
