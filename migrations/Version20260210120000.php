<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Table inscription_tournoi : id, tournoi_id, equipe_id, date_inscription
 */
final class Version20260210120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la table inscription_tournoi';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE inscription_tournoi (id INT AUTO_INCREMENT NOT NULL, tournoi_id INT NOT NULL, equipe_id INT NOT NULL, date_inscription DATETIME NOT NULL, INDEX IDX_INSCRIPTION_TOURNOI (tournoi_id), INDEX IDX_INSCRIPTION_EQUIPE (equipe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inscription_tournoi ADD CONSTRAINT FK_inscription_tournoi_tournoi FOREIGN KEY (tournoi_id) REFERENCES tournoi (id)');
        $this->addSql('ALTER TABLE inscription_tournoi ADD CONSTRAINT FK_inscription_tournoi_equipe FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE inscription_tournoi DROP FOREIGN KEY FK_inscription_tournoi_tournoi');
        $this->addSql('ALTER TABLE inscription_tournoi DROP FOREIGN KEY FK_inscription_tournoi_equipe');
        $this->addSql('DROP TABLE inscription_tournoi');
    }
}
