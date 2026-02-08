<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260207002646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE equipe (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, owner_id INT NOT NULL, INDEX IDX_2449BA157E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE equipe_user (equipe_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_84DA47B76D861B89 (equipe_id), INDEX IDX_84DA47B7A76ED395 (user_id), PRIMARY KEY (equipe_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE equipe_tournament (equipe_id INT NOT NULL, tournament_id INT NOT NULL, INDEX IDX_976CE2286D861B89 (equipe_id), INDEX IDX_976CE22833D1A3E7 (tournament_id), PRIMARY KEY (equipe_id, tournament_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE match_game (id INT AUTO_INCREMENT NOT NULL, date_match DATETIME NOT NULL, score_team1 INT NOT NULL, score_team2 INT NOT NULL, statut VARCHAR(50) NOT NULL, equipe1_id INT NOT NULL, equipe2_id INT NOT NULL, tournament_id INT NOT NULL, INDEX IDX_424480FE4265900C (equipe1_id), INDEX IDX_424480FE50D03FE2 (equipe2_id), INDEX IDX_424480FE33D1A3E7 (tournament_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ranking (id INT AUTO_INCREMENT NOT NULL, nm VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE result (id INT AUTO_INCREMENT NOT NULL, m VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tournament (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE equipe ADD CONSTRAINT FK_2449BA157E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE equipe_user ADD CONSTRAINT FK_84DA47B76D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_user ADD CONSTRAINT FK_84DA47B7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_tournament ADD CONSTRAINT FK_976CE2286D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_tournament ADD CONSTRAINT FK_976CE22833D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE match_game ADD CONSTRAINT FK_424480FE4265900C FOREIGN KEY (equipe1_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE match_game ADD CONSTRAINT FK_424480FE50D03FE2 FOREIGN KEY (equipe2_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE match_game ADD CONSTRAINT FK_424480FE33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA157E3C61F9');
        $this->addSql('ALTER TABLE equipe_user DROP FOREIGN KEY FK_84DA47B76D861B89');
        $this->addSql('ALTER TABLE equipe_user DROP FOREIGN KEY FK_84DA47B7A76ED395');
        $this->addSql('ALTER TABLE equipe_tournament DROP FOREIGN KEY FK_976CE2286D861B89');
        $this->addSql('ALTER TABLE equipe_tournament DROP FOREIGN KEY FK_976CE22833D1A3E7');
        $this->addSql('ALTER TABLE match_game DROP FOREIGN KEY FK_424480FE4265900C');
        $this->addSql('ALTER TABLE match_game DROP FOREIGN KEY FK_424480FE50D03FE2');
        $this->addSql('ALTER TABLE match_game DROP FOREIGN KEY FK_424480FE33D1A3E7');
        $this->addSql('DROP TABLE equipe');
        $this->addSql('DROP TABLE equipe_user');
        $this->addSql('DROP TABLE equipe_tournament');
        $this->addSql('DROP TABLE match_game');
        $this->addSql('DROP TABLE ranking');
        $this->addSql('DROP TABLE result');
        $this->addSql('DROP TABLE tournament');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
