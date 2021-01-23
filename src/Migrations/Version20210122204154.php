<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210122204154 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE claim (id INT AUTO_INCREMENT NOT NULL, application_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_A769DE273E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_claim (user_id INT NOT NULL, claim_id INT NOT NULL, INDEX IDX_45AB257FA76ED395 (user_id), INDEX IDX_45AB257F7096A49F (claim_id), PRIMARY KEY(user_id, claim_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE claim ADD CONSTRAINT FK_A769DE273E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
        $this->addSql('ALTER TABLE user_claim ADD CONSTRAINT FK_45AB257FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_claim ADD CONSTRAINT FK_45AB257F7096A49F FOREIGN KEY (claim_id) REFERENCES claim (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_claim DROP FOREIGN KEY FK_45AB257F7096A49F');
        $this->addSql('DROP TABLE claim');
        $this->addSql('DROP TABLE user_claim');
    }
}
