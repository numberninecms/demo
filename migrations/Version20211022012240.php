<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211022012240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, content_entity_id INT DEFAULT NULL, content LONGTEXT DEFAULT NULL, guest_author_name VARCHAR(255) DEFAULT NULL, guest_author_email VARCHAR(255) DEFAULT NULL, guest_author_url VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_9474526CF675F31B (author_id), INDEX IDX_9474526C727ACA70 (parent_id), INDEX IDX_9474526CB1C2355E (content_entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contententity (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, menu_order INT DEFAULT NULL, published_at DATETIME DEFAULT NULL, title LONGTEXT NOT NULL, status VARCHAR(20) NOT NULL, password VARCHAR(255) DEFAULT NULL, content LONGTEXT DEFAULT NULL, excerpt LONGTEXT DEFAULT NULL, seo JSON DEFAULT NULL, custom_type VARCHAR(255) DEFAULT NULL, custom_fields JSON DEFAULT NULL, comment_status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, content_type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6FD3FC26989D9B62 (slug), INDEX IDX_6FD3FC26F675F31B (author_id), INDEX IDX_6FD3FC26727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contententity_term (id INT AUTO_INCREMENT NOT NULL, content_entity_id INT NOT NULL, term_id INT NOT NULL, position INT NOT NULL, INDEX IDX_17B1182CB1C2355E (content_entity_id), INDEX IDX_17B1182CE2C35FC (term_id), UNIQUE INDEX UNIQ_17B1182CB1C2355EE2C35FC (content_entity_id, term_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contententityrelationship (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, child_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, INDEX IDX_632DB52C727ACA70 (parent_id), INDEX IDX_632DB52CDD62C21B (child_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coreoption (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_863FCDCC5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(191) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE mediafile (id INT NOT NULL, path VARCHAR(1024) DEFAULT NULL, remote_url VARCHAR(1024) DEFAULT NULL, file_size INT DEFAULT NULL, width INT DEFAULT NULL, height INT DEFAULT NULL, duration INT DEFAULT NULL, sizes JSON DEFAULT NULL, exif JSON DEFAULT NULL, keywords JSON DEFAULT NULL, credit VARCHAR(255) DEFAULT NULL, caption VARCHAR(255) DEFAULT NULL, copyright VARCHAR(255) DEFAULT NULL, alternative_text VARCHAR(255) DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, menu_items JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE preset (id INT AUTO_INCREMENT NOT NULL, shortcode_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taxonomy (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, content_types JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE term (id INT AUTO_INCREMENT NOT NULL, taxonomy_id INT NOT NULL, parent_id INT DEFAULT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, custom_fields JSON DEFAULT NULL, UNIQUE INDEX UNIQ_A50FE78D989D9B62 (slug), INDEX IDX_A50FE78D9557E6F6 (taxonomy_id), INDEX IDX_A50FE78D727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE themeoptions (id INT AUTO_INCREMENT NOT NULL, theme VARCHAR(255) NOT NULL, options JSON NOT NULL, draft_options JSON NOT NULL, UNIQUE INDEX UNIQ_F93E29789775E708 (theme), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, display_name_format VARCHAR(255) NOT NULL, custom_fields JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE userrole_user (user_id INT NOT NULL, user_role_id INT NOT NULL, INDEX IDX_FD6105BDA76ED395 (user_id), INDEX IDX_FD6105BD8E0E3CA6 (user_role_id), PRIMARY KEY(user_id, user_role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE userrole (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, capabilities JSON NOT NULL, UNIQUE INDEX UNIQ_F114F21B5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB1C2355E FOREIGN KEY (content_entity_id) REFERENCES contententity (id)');
        $this->addSql('ALTER TABLE contententity ADD CONSTRAINT FK_6FD3FC26F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE contententity ADD CONSTRAINT FK_6FD3FC26727ACA70 FOREIGN KEY (parent_id) REFERENCES contententity (id)');
        $this->addSql('ALTER TABLE contententity_term ADD CONSTRAINT FK_17B1182CB1C2355E FOREIGN KEY (content_entity_id) REFERENCES contententity (id)');
        $this->addSql('ALTER TABLE contententity_term ADD CONSTRAINT FK_17B1182CE2C35FC FOREIGN KEY (term_id) REFERENCES term (id)');
        $this->addSql('ALTER TABLE contententityrelationship ADD CONSTRAINT FK_632DB52C727ACA70 FOREIGN KEY (parent_id) REFERENCES contententity (id)');
        $this->addSql('ALTER TABLE contententityrelationship ADD CONSTRAINT FK_632DB52CDD62C21B FOREIGN KEY (child_id) REFERENCES contententity (id)');
        $this->addSql('ALTER TABLE mediafile ADD CONSTRAINT FK_8AA55D17BF396750 FOREIGN KEY (id) REFERENCES contententity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DBF396750 FOREIGN KEY (id) REFERENCES contententity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE term ADD CONSTRAINT FK_A50FE78D9557E6F6 FOREIGN KEY (taxonomy_id) REFERENCES taxonomy (id)');
        $this->addSql('ALTER TABLE term ADD CONSTRAINT FK_A50FE78D727ACA70 FOREIGN KEY (parent_id) REFERENCES term (id)');
        $this->addSql('ALTER TABLE userrole_user ADD CONSTRAINT FK_FD6105BDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE userrole_user ADD CONSTRAINT FK_FD6105BD8E0E3CA6 FOREIGN KEY (user_role_id) REFERENCES userrole (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C727ACA70');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB1C2355E');
        $this->addSql('ALTER TABLE contententity DROP FOREIGN KEY FK_6FD3FC26727ACA70');
        $this->addSql('ALTER TABLE contententity_term DROP FOREIGN KEY FK_17B1182CB1C2355E');
        $this->addSql('ALTER TABLE contententityrelationship DROP FOREIGN KEY FK_632DB52C727ACA70');
        $this->addSql('ALTER TABLE contententityrelationship DROP FOREIGN KEY FK_632DB52CDD62C21B');
        $this->addSql('ALTER TABLE mediafile DROP FOREIGN KEY FK_8AA55D17BF396750');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DBF396750');
        $this->addSql('ALTER TABLE term DROP FOREIGN KEY FK_A50FE78D9557E6F6');
        $this->addSql('ALTER TABLE contententity_term DROP FOREIGN KEY FK_17B1182CE2C35FC');
        $this->addSql('ALTER TABLE term DROP FOREIGN KEY FK_A50FE78D727ACA70');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE contententity DROP FOREIGN KEY FK_6FD3FC26F675F31B');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE userrole_user DROP FOREIGN KEY FK_FD6105BDA76ED395');
        $this->addSql('ALTER TABLE userrole_user DROP FOREIGN KEY FK_FD6105BD8E0E3CA6');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE contententity');
        $this->addSql('DROP TABLE contententity_term');
        $this->addSql('DROP TABLE contententityrelationship');
        $this->addSql('DROP TABLE coreoption');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE mediafile');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE preset');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE taxonomy');
        $this->addSql('DROP TABLE term');
        $this->addSql('DROP TABLE themeoptions');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE userrole_user');
        $this->addSql('DROP TABLE userrole');
    }
}
