<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration: Change Tournoi::$cagnotte from DOUBLE to DECIMAL(10,2)
 * 
 * Pour éviter les imprécisions de calcul en virgule flottante sur les montants,
 * on convertit le champ cagnotte du type DOUBLE PRECISION au type DECIMAL(10,2).
 */
final class Version20260302221000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change Tournoi cagnotte column from DOUBLE to DECIMAL(10,2)';
    }

    public function up(Schema $schema): void
    {
        // Conversion du champ cagnotte de DOUBLE vers DECIMAL(10, 2)
        $this->addSql('ALTER TABLE tournoi CHANGE cagnotte cagnotte DECIMAL(10, 2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Restauration du type d'origine en cas de rollback
        $this->addSql('ALTER TABLE tournoi CHANGE cagnotte cagnotte DOUBLE PRECISION DEFAULT NULL');
    }
}
