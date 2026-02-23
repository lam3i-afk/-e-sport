<?php

namespace App\MessageHandler;

use App\Message\GenerateMatchesMessage;
use App\Repository\TournoiRepository;
use App\Service\MatchGeneratorService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler qui traite le message GenerateMatchesMessage :
 * cherche les tournois dont la date limite est passée et génère les matchs.
 */
#[AsMessageHandler]
class GenerateMatchesHandler
{
    public function __construct(
        private TournoiRepository $tournoiRepository,
        private MatchGeneratorService $matchGenerator,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(GenerateMatchesMessage $message): void
    {
        $now = new \DateTime();
        $tournois = $this->tournoiRepository->findTournoisReadyForGeneration($now);

        if (empty($tournois)) {
            $this->logger->info('[Scheduler] Aucun tournoi prêt pour la génération.');
            return;
        }

        foreach ($tournois as $tournoi) {
            $nbMatchs = $this->matchGenerator->generateIfReady($tournoi);

            if ($nbMatchs > 0) {
                $this->logger->info(sprintf(
                    '[Scheduler] Tournoi #%d "%s" : %d matchs générés.',
                    $tournoi->getId(),
                    $tournoi->getNom(),
                    $nbMatchs
                ));
            } else {
                $this->logger->warning(sprintf(
                    '[Scheduler] Tournoi #%d "%s" : pas assez d\'équipes, aucun match.',
                    $tournoi->getId(),
                    $tournoi->getNom()
                ));
            }
        }
    }
}
