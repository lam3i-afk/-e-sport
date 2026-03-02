<?php

namespace App\Service;

use App\Entity\Jeu;

/**
 * Service métier lié aux entités "Jeu".
 *
 * Cette classe contient la logique de validation et autres règles de
 * gestion applicables aux objets Jeu. Les méthodes illustrent des
 * exemples de vérifications et retournent un booléen ou lèvent une
 * exception en cas de violation de règle.
 *
 * Lorsqu'un projet évolue il est courant d'ajouter ou de modifier ces
 * règles directement dans le service pour centraliser la logique métier.
 */
class JeuService
{
    /**
     * Valide un objet Jeu selon un ensemble de règles métier.
     *
     * Si un jeu ne respecte pas une règle, on lance une exception
     * \InvalidArgumentException avec un message explicite. Cette méthode
     * est utilisée par les tests unitaires pour garantir la conformité.
     *
     * @param Jeu $jeu l'entité à valider
     * @return bool vrai si toutes les règles sont respectées
     */
    public function validate(Jeu $jeu): bool
    {
        // vérifie que le nom n'est pas vide
        if (empty($jeu->getNom())) {
            throw new \InvalidArgumentException('Le nom du jeu ne peut pas être vide');
        }
        // s'assure que le nom ne dépasse pas la longueur autorisée
        if (strlen($jeu->getNom()) > 255) {
            throw new \InvalidArgumentException('Le nom du jeu ne peut pas dépasser 255 caractères');
        }

        // on peut ajouter ici d'autres règles sur le genre si nécessaire
        if (empty($jeu->getGenre())) {
            throw new \InvalidArgumentException('Le genre ne peut pas être vide');
        }

        // la plateforme est requise pour chaque jeu
        if (empty($jeu->getPlateforme())) {
            throw new \InvalidArgumentException('La plateforme ne peut pas être vide');
        }

        // description non vide et longueur minimale
        if (empty($jeu->getDescription())) {
            throw new \InvalidArgumentException('La description ne peut pas être vide');
        }
        if (strlen($jeu->getDescription()) < 10) {
            throw new \InvalidArgumentException('La description doit contenir au moins 10 caractères');
        }

        // statut doit appartenir à un ensemble prédéfini de valeurs
        $allowedStatuses = ['ACTIVE', 'INACTIVE', 'COMING_SOON'];
        if (!in_array($jeu->getStatut(), $allowedStatuses, true)) {
            throw new \InvalidArgumentException('Le statut doit être ACTIVE, INACTIVE ou COMING_SOON');
        }

        // si on arrive ici, toutes les règles sont passées
        return true;
    }

    /**
     * Ensure a player's score for a game is not negative.
     *
     * @param int $score
     * @return bool
     */
    public function isScoreValid(int $score): bool
    {
        // un score ne doit jamais être négatif, sinon on considère que c'est
        // une donnée invalide (ex. triche ou erreur de saisie)
        return $score >= 0;
    }

    /**
     * Determine if a status transition is allowed.
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function canTransition(string $from, string $to): bool
    {
        // exemple d'ensemble de transitions autorisées entre statuts.
        // dans une vraie application, on stockerait peut-être ces règles
        // dans la base ou les configurerait ailleurs.
        $allowed = [
            'DRAFT' => ['PUBLISHED'],
            'PUBLISHED' => ['ARCHIVED'],
            'ARCHIVED' => [],
        ];

        // on vérifie que le statut de départ existe et que la cible est
        // dans la liste des statuts permis pour ce départ.
        return isset($allowed[$from]) && in_array($to, $allowed[$from], true);
    }

    /**
     * Check if the number of players is within allowed limits.
     *
     * @param int $players
     * @param int $max
     * @return bool
     */
    public function isPlayerCountAllowed(int $players, int $max): bool
    {
        // contrôle simple: le nombre de joueurs ne doit pas dépasser
        // la limite définie pour le type de jeu
        return $players <= $max;
    }
}

