<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240216224503 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE programming_forum_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE programming_forum_post_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE programming_forum_rating_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE programming_forum_reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE programming_forum_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE programming_forum_topic_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE programming_forum_category (id INT NOT NULL, parent_category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F8998196796A8F92 ON programming_forum_category (parent_category_id)');
        $this->addSql('CREATE TABLE programming_forum_post (id INT NOT NULL, topic_id INT DEFAULT NULL, user_id UUID DEFAULT NULL, content TEXT NOT NULL, content_to_accept TEXT DEFAULT NULL, accepted BOOLEAN DEFAULT false NOT NULL, edit_accepted BOOLEAN DEFAULT true, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5018952F1F55203D ON programming_forum_post (topic_id)');
        $this->addSql('CREATE INDEX IDX_5018952FA76ED395 ON programming_forum_post (user_id)');
        $this->addSql('COMMENT ON COLUMN programming_forum_post.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE programming_forum_rating (id INT NOT NULL, user_id UUID NOT NULL, post_id INT NOT NULL, points INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_80D9B863A76ED395 ON programming_forum_rating (user_id)');
        $this->addSql('CREATE INDEX IDX_80D9B8634B89032C ON programming_forum_rating (post_id)');
        $this->addSql('COMMENT ON COLUMN programming_forum_rating.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE programming_forum_reset_password_request (id INT NOT NULL, user_id UUID NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_14CC4BE1A76ED395 ON programming_forum_reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN programming_forum_reset_password_request.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN programming_forum_reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN programming_forum_reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE programming_forum_tag (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE programming_forum_topic (id INT NOT NULL, category_id INT DEFAULT NULL, user_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, accepted BOOLEAN DEFAULT false NOT NULL, closed BOOLEAN DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A5928E2612469DE2 ON programming_forum_topic (category_id)');
        $this->addSql('CREATE INDEX IDX_A5928E26A76ED395 ON programming_forum_topic (user_id)');
        $this->addSql('COMMENT ON COLUMN programming_forum_topic.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE programming_forum_topic_tag (topic_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(topic_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_C5DBD7EE1F55203D ON programming_forum_topic_tag (topic_id)');
        $this->addSql('CREATE INDEX IDX_C5DBD7EEBAD26311 ON programming_forum_topic_tag (tag_id)');
        $this->addSql('CREATE TABLE "programming_forum_user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, login VARCHAR(255) NOT NULL, banned TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, gender VARCHAR(255) NOT NULL, level VARCHAR(255) DEFAULT \'new\' NOT NULL, avatar VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_87012FEBE7927C74 ON "programming_forum_user" (email)');
        $this->addSql('COMMENT ON COLUMN "programming_forum_user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE programming_forum_category ADD CONSTRAINT FK_F8998196796A8F92 FOREIGN KEY (parent_category_id) REFERENCES programming_forum_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE programming_forum_post ADD CONSTRAINT FK_5018952F1F55203D FOREIGN KEY (topic_id) REFERENCES programming_forum_topic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE programming_forum_post ADD CONSTRAINT FK_5018952FA76ED395 FOREIGN KEY (user_id) REFERENCES "programming_forum_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE programming_forum_rating ADD CONSTRAINT FK_80D9B863A76ED395 FOREIGN KEY (user_id) REFERENCES "programming_forum_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE programming_forum_rating ADD CONSTRAINT FK_80D9B8634B89032C FOREIGN KEY (post_id) REFERENCES programming_forum_post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE programming_forum_reset_password_request ADD CONSTRAINT FK_14CC4BE1A76ED395 FOREIGN KEY (user_id) REFERENCES "programming_forum_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE programming_forum_topic ADD CONSTRAINT FK_A5928E2612469DE2 FOREIGN KEY (category_id) REFERENCES programming_forum_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE programming_forum_topic ADD CONSTRAINT FK_A5928E26A76ED395 FOREIGN KEY (user_id) REFERENCES "programming_forum_user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE programming_forum_topic_tag ADD CONSTRAINT FK_C5DBD7EE1F55203D FOREIGN KEY (topic_id) REFERENCES programming_forum_topic (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE programming_forum_topic_tag ADD CONSTRAINT FK_C5DBD7EEBAD26311 FOREIGN KEY (tag_id) REFERENCES programming_forum_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE programming_forum_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE programming_forum_post_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE programming_forum_rating_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE programming_forum_reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE programming_forum_tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE programming_forum_topic_id_seq CASCADE');
        $this->addSql('ALTER TABLE programming_forum_category DROP CONSTRAINT FK_F8998196796A8F92');
        $this->addSql('ALTER TABLE programming_forum_post DROP CONSTRAINT FK_5018952F1F55203D');
        $this->addSql('ALTER TABLE programming_forum_post DROP CONSTRAINT FK_5018952FA76ED395');
        $this->addSql('ALTER TABLE programming_forum_rating DROP CONSTRAINT FK_80D9B863A76ED395');
        $this->addSql('ALTER TABLE programming_forum_rating DROP CONSTRAINT FK_80D9B8634B89032C');
        $this->addSql('ALTER TABLE programming_forum_reset_password_request DROP CONSTRAINT FK_14CC4BE1A76ED395');
        $this->addSql('ALTER TABLE programming_forum_topic DROP CONSTRAINT FK_A5928E2612469DE2');
        $this->addSql('ALTER TABLE programming_forum_topic DROP CONSTRAINT FK_A5928E26A76ED395');
        $this->addSql('ALTER TABLE programming_forum_topic_tag DROP CONSTRAINT FK_C5DBD7EE1F55203D');
        $this->addSql('ALTER TABLE programming_forum_topic_tag DROP CONSTRAINT FK_C5DBD7EEBAD26311');
        $this->addSql('DROP TABLE programming_forum_category');
        $this->addSql('DROP TABLE programming_forum_post');
        $this->addSql('DROP TABLE programming_forum_rating');
        $this->addSql('DROP TABLE programming_forum_reset_password_request');
        $this->addSql('DROP TABLE programming_forum_tag');
        $this->addSql('DROP TABLE programming_forum_topic');
        $this->addSql('DROP TABLE programming_forum_topic_tag');
        $this->addSql('DROP TABLE "programming_forum_user"');
    }
}
