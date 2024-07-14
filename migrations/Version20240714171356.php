<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240714171356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD debut TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD fin TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE evenement DROP jour');
        $this->addSql('ALTER TABLE evenement DROP heure_debut');
        $this->addSql('ALTER TABLE evenement DROP heure_fin');
        $this->addSql('ALTER TABLE utilisateur ADD prenom VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD nom VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE utilisateur DROP prenom');
        $this->addSql('ALTER TABLE utilisateur DROP nom');
        $this->addSql('ALTER TABLE evenement ADD jour DATE NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD heure_debut TIME(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD heure_fin TIME(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE evenement DROP debut');
        $this->addSql('ALTER TABLE evenement DROP fin');
    }
}
