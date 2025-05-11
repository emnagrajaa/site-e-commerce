<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417150201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE person_skill (person_id INT NOT NULL, skill_id INT NOT NULL, INDEX IDX_F20BFBB3217BBB47 (person_id), INDEX IDX_F20BFBB35585C142 (skill_id), PRIMARY KEY(person_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skill (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE person_skill ADD CONSTRAINT FK_F20BFBB3217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE person_skill ADD CONSTRAINT FK_F20BFBB35585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person_skill DROP FOREIGN KEY FK_F20BFBB3217BBB47');
        $this->addSql('ALTER TABLE person_skill DROP FOREIGN KEY FK_F20BFBB35585C142');
        $this->addSql('DROP TABLE person_skill');
        $this->addSql('DROP TABLE skill');
    }
}
