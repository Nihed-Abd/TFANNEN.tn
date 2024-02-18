<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218105931 extends AbstractMigration
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
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, livraison_id INT DEFAULT NULL, promotion_id INT DEFAULT NULL, adresse VARCHAR(255) NOT NULL, num_tel INT NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_6EEAA67D8E54FB25 (livraison_id), INDEX IDX_6EEAA67D139DF194 (promotion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, description VARCHAR(255) NOT NULL, recompense VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_design (competition_id INT NOT NULL, design_id INT NOT NULL, INDEX IDX_A20C9AE37B39D312 (competition_id), INDEX IDX_A20C9AE3E41DC9B2 (design_id), PRIMARY KEY(competition_id, design_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE design (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, categorie VARCHAR(255) NOT NULL, picture VARCHAR(255) NOT NULL, INDEX IDX_CD4F5A3067B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE design_commande (design_id INT NOT NULL, commande_id INT NOT NULL, INDEX IDX_B5C8C43BE41DC9B2 (design_id), INDEX IDX_B5C8C43B82EA2E54 (commande_id), PRIMARY KEY(design_id, commande_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, participation_id INT DEFAULT NULL, date DATE NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, lieu VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B26681E6ACE3B73 (participation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livraison (id INT AUTO_INCREMENT NOT NULL, facture VARCHAR(255) NOT NULL, livreur VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, score DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation_user (participation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_51E769006ACE3B73 (participation_id), INDEX IDX_51E76900A76ED395 (user_id), PRIMARY KEY(participation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE promotion (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, taux DOUBLE PRECISION NOT NULL, code_promo INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, objet VARCHAR(255) NOT NULL, type_de_reclamation VARCHAR(255) NOT NULL, description_reclamation VARCHAR(255) NOT NULL, date DATE NOT NULL, INDEX IDX_CE606404A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, id_reclamation_id INT NOT NULL, status VARCHAR(255) NOT NULL, decision VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5FB6DEC7100D1FDF (id_reclamation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0E41DC9B2 FOREIGN KEY (design_id) REFERENCES design (id)');
        $this->addSql('ALTER TABLE carriere ADD CONSTRAINT FK_FB9C1FE4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D8E54FB25 FOREIGN KEY (livraison_id) REFERENCES livraison (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id)');
        $this->addSql('ALTER TABLE competition_design ADD CONSTRAINT FK_A20C9AE37B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competition_design ADD CONSTRAINT FK_A20C9AE3E41DC9B2 FOREIGN KEY (design_id) REFERENCES design (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE design ADD CONSTRAINT FK_CD4F5A3067B3B43D FOREIGN KEY (users_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE design_commande ADD CONSTRAINT FK_B5C8C43BE41DC9B2 FOREIGN KEY (design_id) REFERENCES design (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE design_commande ADD CONSTRAINT FK_B5C8C43B82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E6ACE3B73 FOREIGN KEY (participation_id) REFERENCES participation (id)');
        $this->addSql('ALTER TABLE participation_user ADD CONSTRAINT FK_51E769006ACE3B73 FOREIGN KEY (participation_id) REFERENCES participation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participation_user ADD CONSTRAINT FK_51E76900A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7100D1FDF FOREIGN KEY (id_reclamation_id) REFERENCES reclamation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0E41DC9B2');
        $this->addSql('ALTER TABLE carriere DROP FOREIGN KEY FK_FB9C1FE4A76ED395');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D8E54FB25');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D139DF194');
        $this->addSql('ALTER TABLE competition_design DROP FOREIGN KEY FK_A20C9AE37B39D312');
        $this->addSql('ALTER TABLE competition_design DROP FOREIGN KEY FK_A20C9AE3E41DC9B2');
        $this->addSql('ALTER TABLE design DROP FOREIGN KEY FK_CD4F5A3067B3B43D');
        $this->addSql('ALTER TABLE design_commande DROP FOREIGN KEY FK_B5C8C43BE41DC9B2');
        $this->addSql('ALTER TABLE design_commande DROP FOREIGN KEY FK_B5C8C43B82EA2E54');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E6ACE3B73');
        $this->addSql('ALTER TABLE participation_user DROP FOREIGN KEY FK_51E769006ACE3B73');
        $this->addSql('ALTER TABLE participation_user DROP FOREIGN KEY FK_51E76900A76ED395');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A76ED395');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC7100D1FDF');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE carriere');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE competition_design');
        $this->addSql('DROP TABLE design');
        $this->addSql('DROP TABLE design_commande');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE livraison');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE participation_user');
        $this->addSql('DROP TABLE promotion');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
