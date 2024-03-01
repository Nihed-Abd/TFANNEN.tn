<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240212161312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, design_id INT DEFAULT NULL, avis DOUBLE PRECISION NOT NULL, commentaire VARCHAR(255) NOT NULL, INDEX IDX_8F91ABF0E41DC9B2 (design_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carriere (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, cv VARCHAR(255) NOT NULL, score DOUBLE PRECISION NOT NULL, titre VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_FB9C1FE4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, description VARCHAR(255) NOT NULL, recompense VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_design (competition_id INT NOT NULL, design_id INT NOT NULL, INDEX IDX_A20C9AE37B39D312 (competition_id), INDEX IDX_A20C9AE3E41DC9B2 (design_id), PRIMARY KEY(competition_id, design_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, participation_id INT DEFAULT NULL, date DATE NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, lieu VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B26681E6ACE3B73 (participation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, score DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation_user (participation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_51E769006ACE3B73 (participation_id), INDEX IDX_51E76900A76ED395 (user_id), PRIMARY KEY(participation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promotion (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, taux DOUBLE PRECISION NOT NULL, code_promo INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0E41DC9B2 FOREIGN KEY (design_id) REFERENCES design (id)');
        $this->addSql('ALTER TABLE carriere ADD CONSTRAINT FK_FB9C1FE4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE competition_design ADD CONSTRAINT FK_A20C9AE37B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competition_design ADD CONSTRAINT FK_A20C9AE3E41DC9B2 FOREIGN KEY (design_id) REFERENCES design (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E6ACE3B73 FOREIGN KEY (participation_id) REFERENCES participation (id)');
        $this->addSql('ALTER TABLE participation_user ADD CONSTRAINT FK_51E769006ACE3B73 FOREIGN KEY (participation_id) REFERENCES participation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation_user ADD CONSTRAINT FK_51E76900A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commande ADD promotion_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D139DF194 ON commande (promotion_id)');
        $this->addSql('ALTER TABLE design ADD users_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE design ADD CONSTRAINT FK_CD4F5A3067B3B43D FOREIGN KEY (users_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CD4F5A3067B3B43D ON design (users_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D139DF194');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0E41DC9B2');
        $this->addSql('ALTER TABLE carriere DROP FOREIGN KEY FK_FB9C1FE4A76ED395');
        $this->addSql('ALTER TABLE competition_design DROP FOREIGN KEY FK_A20C9AE37B39D312');
        $this->addSql('ALTER TABLE competition_design DROP FOREIGN KEY FK_A20C9AE3E41DC9B2');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E6ACE3B73');
        $this->addSql('ALTER TABLE participation_user DROP FOREIGN KEY FK_51E769006ACE3B73');
        $this->addSql('ALTER TABLE participation_user DROP FOREIGN KEY FK_51E76900A76ED395');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE carriere');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE competition_design');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE participation_user');
        $this->addSql('DROP TABLE promotion');
        $this->addSql('DROP INDEX IDX_6EEAA67D139DF194 ON commande');
        $this->addSql('ALTER TABLE commande DROP promotion_id');
        $this->addSql('ALTER TABLE design DROP FOREIGN KEY FK_CD4F5A3067B3B43D');
        $this->addSql('DROP INDEX IDX_CD4F5A3067B3B43D ON design');
        $this->addSql('ALTER TABLE design DROP users_id');
    }
}
