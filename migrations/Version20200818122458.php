<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200818122458 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE thread_id_seq CASCADE');
        $this->addSql('CREATE TABLE tag_topic (tag_id INT NOT NULL, topic_id INT NOT NULL, PRIMARY KEY(tag_id, topic_id))');
        $this->addSql('CREATE INDEX IDX_BFACC71DBAD26311 ON tag_topic (tag_id)');
        $this->addSql('CREATE INDEX IDX_BFACC71D1F55203D ON tag_topic (topic_id)');
        $this->addSql('ALTER TABLE tag_topic ADD CONSTRAINT FK_BFACC71DBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_topic ADD CONSTRAINT FK_BFACC71D1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE thread_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE tag_topic');
    }
}
