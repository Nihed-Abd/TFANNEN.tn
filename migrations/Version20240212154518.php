<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240212154518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE design (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, categorie VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE design_commande (design_id INT NOT NULL, commande_id INT NOT NULL, INDEX IDX_B5C8C43BE41DC9B2 (design_id), INDEX IDX_B5C8C43B82EA2E54 (commande_id), PRIMARY KEY(design_id, commande_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE design_commande ADD CONSTRAINT FK_B5C8C43BE41DC9B2 FOREIGN KEY (design_id) REFERENCES design (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE design_commande ADD CONSTRAINT FK_B5C8C43B82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE design_commande DROP FOREIGN KEY FK_B5C8C43BE41DC9B2');
        $this->addSql('ALTER TABLE design_commande DROP FOREIGN KEY FK_B5C8C43B82EA2E54');
        $this->addSql('DROP TABLE design');
        $this->addSql('DROP TABLE design_commande');
    }
}
