<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200817223752 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag_thread (tag_id INT NOT NULL, thread_id INT NOT NULL, PRIMARY KEY(tag_id, thread_id))');
        $this->addSql('CREATE INDEX IDX_D86105AFBAD26311 ON tag_thread (tag_id)');
        $this->addSql('CREATE INDEX IDX_D86105AFE2904019 ON tag_thread (thread_id)');
        $this->addSql('CREATE TABLE thread_tag (thread_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(thread_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_CD4E790E2904019 ON thread_tag (thread_id)');
        $this->addSql('CREATE INDEX IDX_CD4E790BAD26311 ON thread_tag (tag_id)');
        $this->addSql('ALTER TABLE tag_thread ADD CONSTRAINT FK_D86105AFBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_thread ADD CONSTRAINT FK_D86105AFE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE thread_tag ADD CONSTRAINT FK_CD4E790E2904019 FOREIGN KEY (thread_id) REFERENCES thread (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE thread_tag ADD CONSTRAINT FK_CD4E790BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE tag_thread');
        $this->addSql('DROP TABLE thread_tag');
    }
}
