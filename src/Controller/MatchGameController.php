<?php

namespace App\Controller;

use App\Entity\MatchGame;
use App\Form\MatchGame1Type;
use App\Repository\MatchGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/match/game')]
final class MatchGameController extends AbstractController
{
 #[Route("/", name: 'app_match_game_index', methods: ['GET'])]
    public function index(MatchGameRepository $matchGameRepository): Response
    {
        return $this->render('match_game/index.html.twig', [
            'match_games' => $matchGameRepository->findAll(),
        ]);
    }

        #[Route("/dashboard" ,name: 'app_match_game_index2', methods: ['GET'])]
    public function index2(MatchGameRepository $matchGameRepository): Response
    {
        return $this->render('match_game/MatchDashbored.html.twig', [
            'match_games' => $matchGameRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_match_game_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $matchGame = new MatchGame();
        $form = $this->createForm(MatchGame1Type::class, $matchGame);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // تأكد match مربوط بتورنو
            if (!$matchGame->getTournoi()) {
                $this->addFlash('error', 'Match doit appartenir à un tournoi');
                return $this->redirectToRoute('app_match_game_new');
            }

            $entityManager->persist($matchGame);
            $entityManager->flush();

            return $this->redirectToRoute('app_match_game_index');
        }

        return $this->render('match_game/new.html.twig', [
            'match_game' => $matchGame,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_match_game_show', methods: ['GET'])]
    public function show(MatchGame $matchGame): Response
    {
        return $this->render('match_game/show.html.twig', [
            'match_game' => $matchGame,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_match_game_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MatchGame $matchGame, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MatchGame1Type::class, $matchGame);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$matchGame->getTournoi()) {
                $this->addFlash('error', 'Match doit appartenir à un tournoi');
                return $this->redirectToRoute('app_match_game_edit', [
                    'id' => $matchGame->getId()
                ]);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_match_game_index');
        }

        return $this->render('match_game/edit.html.twig', [
            'match_game' => $matchGame,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_match_game_delete', methods: ['POST'])]
    public function delete(Request $request, MatchGame $matchGame, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$matchGame->getId(), $request->request->get('_token'))) {
            $entityManager->remove($matchGame);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_match_game_index');
    }
}
