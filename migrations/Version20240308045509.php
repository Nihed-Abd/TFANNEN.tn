<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240308045509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE competition ADD name VARCHAR(255) NOT NULL, ADD regle VARCHAR(255) NOT NULL, ADD date_fin DATE NOT NULL, CHANGE date date_debut DATE NOT NULL');
        $this->addSql('ALTER TABLE promotion ADD name VARCHAR(255) NOT NULL, ADD description VARCHAR(255) NOT NULL, ADD date_fin DATE NOT NULL, ADD type_promo VARCHAR(255) NOT NULL, CHANGE code_promo code_promo VARCHAR(255) NOT NULL, CHANGE taux taux_reduction DOUBLE PRECISION NOT NULL, CHANGE date date_debut DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE competition ADD date DATE NOT NULL, DROP name, DROP regle, DROP date_debut, DROP date_fin');
        $this->addSql('ALTER TABLE promotion ADD date DATE NOT NULL, DROP name, DROP description, DROP date_debut, DROP date_fin, DROP type_promo, CHANGE code_promo code_promo INT NOT NULL, CHANGE taux_reduction taux DOUBLE PRECISION NOT NULL');
    }
}
