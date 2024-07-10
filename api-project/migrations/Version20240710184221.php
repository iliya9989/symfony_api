<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240710184221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE character ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE character ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE character ALTER gender TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE character ALTER ability TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE character ALTER ability DROP NOT NULL');
        $this->addSql('ALTER TABLE character ALTER minimal_distance TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE character ALTER weight TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE nemesis DROP CONSTRAINT "character"');
        $this->addSql('DROP INDEX "fki_Character Id"');
        $this->addSql('DROP INDEX IDX_5802E4831136BE75');
        $this->addSql('ALTER TABLE nemesis ADD character_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE nemesis DROP character_id');
        $this->addSql('ALTER TABLE nemesis ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE nemesis ADD CONSTRAINT FK_5802E48381877935 FOREIGN KEY (character_id_id) REFERENCES character (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5802E48381877935 ON nemesis (character_id_id)');
        $this->addSql('ALTER TABLE secret DROP CONSTRAINT nemesis');
        $this->addSql('DROP INDEX IDX_5CA2E8E5512E3775');
        $this->addSql('ALTER TABLE secret ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE secret ALTER secret_code TYPE INT');
        $this->addSql('ALTER TABLE secret RENAME COLUMN nemesis_id TO nemesis_id_id');
        $this->addSql('ALTER TABLE secret ADD CONSTRAINT FK_5CA2E8E578AAC221 FOREIGN KEY (nemesis_id_id) REFERENCES nemesis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5CA2E8E578AAC221 ON secret (nemesis_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE nemesis DROP CONSTRAINT FK_5802E48381877935');
        $this->addSql('DROP INDEX IDX_5802E48381877935');
        $this->addSql('ALTER TABLE nemesis ADD character_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nemesis DROP character_id_id');
        $this->addSql('CREATE SEQUENCE nemesis_id_seq');
        $this->addSql('SELECT setval(\'nemesis_id_seq\', (SELECT MAX(id) FROM nemesis))');
        $this->addSql('ALTER TABLE nemesis ALTER id SET DEFAULT nextval(\'nemesis_id_seq\')');
        $this->addSql('ALTER TABLE nemesis ADD CONSTRAINT "character" FOREIGN KEY (character_id) REFERENCES "character" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX "fki_Character Id" ON nemesis (id)');
        $this->addSql('CREATE INDEX IDX_5802E4831136BE75 ON nemesis (character_id)');
        $this->addSql('CREATE SEQUENCE character_id_seq');
        $this->addSql('SELECT setval(\'character_id_seq\', (SELECT MAX(id) FROM character))');
        $this->addSql('ALTER TABLE character ALTER id SET DEFAULT nextval(\'character_id_seq\')');
        $this->addSql('ALTER TABLE character ALTER name TYPE TEXT');
        $this->addSql('ALTER TABLE character ALTER gender TYPE TEXT');
        $this->addSql('ALTER TABLE character ALTER ability TYPE TEXT');
        $this->addSql('ALTER TABLE character ALTER ability SET NOT NULL');
        $this->addSql('ALTER TABLE character ALTER minimal_distance TYPE NUMERIC(10, 0)');
        $this->addSql('ALTER TABLE character ALTER weight TYPE NUMERIC(10, 0)');
        $this->addSql('ALTER TABLE secret DROP CONSTRAINT FK_5CA2E8E578AAC221');
        $this->addSql('DROP INDEX IDX_5CA2E8E578AAC221');
        $this->addSql('CREATE SEQUENCE secret_id_seq');
        $this->addSql('SELECT setval(\'secret_id_seq\', (SELECT MAX(id) FROM secret))');
        $this->addSql('ALTER TABLE secret ALTER id SET DEFAULT nextval(\'secret_id_seq\')');
        $this->addSql('ALTER TABLE secret ALTER secret_code TYPE BIGINT');
        $this->addSql('ALTER TABLE secret RENAME COLUMN nemesis_id_id TO nemesis_id');
        $this->addSql('ALTER TABLE secret ADD CONSTRAINT nemesis FOREIGN KEY (nemesis_id) REFERENCES nemesis (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5CA2E8E5512E3775 ON secret (nemesis_id)');
    }
}
