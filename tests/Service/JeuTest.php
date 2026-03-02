<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use App\Entity\Jeu;
use App\Service\JeuService;

class JeuTest extends TestCase
{
    private function createValidJeu(): Jeu
    {
        $jeu = new Jeu();
        $jeu->setNom('Super Game');
        $jeu->setGenre('Adventure');
        $jeu->setPlateforme('PC');
        $jeu->setDescription('Un jeu d\'aventure passionnant.');
        $jeu->setStatut('ACTIVE');

        return $jeu;
    }

    public function testValidJeu(): void
    {
        $service = new JeuService();
        $result = $service->validate($this->createValidJeu());
        $this->assertTrue($result);
    }

    // --- selected business rules ------------------------------------------------

    public function testJeuNomTropLong(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le nom du jeu ne peut pas dépasser 255 caractères');

        $jeu = $this->createValidJeu();
        $jeu->setNom(str_repeat('J', 256));
        (new JeuService())->validate($jeu);
    }

    public function testJeuSansPlateforme(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('La plateforme ne peut pas être vide');

        $jeu = $this->createValidJeu();
        $jeu->setPlateforme('');
        (new JeuService())->validate($jeu);
    }

    public function testJeuDescriptionTropCourte(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('La description doit contenir au moins 10 caractères');

        $jeu = $this->createValidJeu();
        $jeu->setDescription('tropcour');
        (new JeuService())->validate($jeu);
    }

    public function testJeuStatutInvalide(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Le statut doit être ACTIVE, INACTIVE ou COMING_SOON');

        $jeu = $this->createValidJeu();
        $jeu->setStatut('UNKNOWN');
        (new JeuService())->validate($jeu);
    }

    // other rules have been omitted per request

    // Additional tests could exercise isScoreValid, canTransition, etc.
}


