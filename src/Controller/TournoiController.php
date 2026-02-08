<?php

namespace App\Controller;

use App\Entity\Tournoi;
use App\Form\TournoiType;
use App\Repository\TournoiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/tournoi')]
final class TournoiController extends AbstractController
{
    #[Route(name: 'app_tournoi_index', methods: ['GET'])]
    public function index(\Symfony\Component\HttpFoundation\Request $request, TournoiRepository $tournoiRepository): Response
    {
        $status = $request->query->get('status');
        $type = $request->query->get('type');
        $search = $request->query->get('q');

        $tournois = $tournoiRepository->findByFilters($status, $type, $search);

        return $this->render('tournoi/index.html.twig', [
            'tournois' => $tournois,
            'currentStatus' => $status,
            'currentType' => $type,
            'searchTerm' => $search,
        ]);
    }

    #[Route('/new', name: 'app_tournoi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tournoi = new Tournoi();
        $form = $this->createForm(TournoiType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tournoi);
            $entityManager->flush();

            return $this->redirectToRoute('app_tournoi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournoi/new.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournoi_show', methods: ['GET'])]
    public function show(int $id, TournoiRepository $tournoiRepository): Response
    {
        $tournoi = $tournoiRepository->findOneWithJeu($id);
        
        if (!$tournoi) {
            throw $this->createNotFoundException('Tournament not found');
        }

        return $this->render('tournoi/show.html.twig', [
            'tournoi' => $tournoi,
        ]);
    }

    #[Route('/{id}/inscription', name: 'app_tournoi_register', methods: ['POST'])]
    public function register(int $id, Request $request, TournoiRepository $tournoiRepository, EntityManagerInterface $entityManager): Response
    {
        $tournoi = $tournoiRepository->findOneWithJeu($id);
        if (!$tournoi) {
            throw $this->createNotFoundException('Tournoi introuvable.');
        }

        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour vous inscrire.');
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('register'.$id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_tournoi_show', ['id' => $id]);
        }

        // Vérifier limite d'inscription si définie
        if ($tournoi->getDateInscriptionLimite() && new \DateTime() > $tournoi->getDateInscriptionLimite()) {
            $this->addFlash('error', 'La période d\'inscription est terminée.');
            return $this->redirectToRoute('app_tournoi_show', ['id' => $id]);
        }

        // Vérifier si déjà inscrit
        if ($tournoi->getParticipants()->contains($user)) {
            $this->addFlash('info', 'Vous êtes déjà inscrit à ce tournoi.');
            return $this->redirectToRoute('app_tournoi_show', ['id' => $id]);
        }

        // Vérifier la capacité maximale si définie
        $max = $tournoi->getMaxParticipants() ?: 0;
        $current = $tournoi->getParticipants()->count();
        if ($max > 0 && $current >= $max) {
            $this->addFlash('error', 'Le tournoi est complet.');
            return $this->redirectToRoute('app_tournoi_show', ['id' => $id]);
        }

        $tournoi->addParticipant($user);
        $entityManager->persist($tournoi);
        $entityManager->flush();

        $this->addFlash('success', 'Inscription réussie.');
        return $this->redirectToRoute('app_tournoi_show', ['id' => $id]);
    }

    #[Route('/{id}/edit', name: 'app_tournoi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tournoi $tournoi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TournoiType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tournoi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournoi/edit.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tournoi_delete', methods: ['POST'])]
    public function delete(Request $request, Tournoi $tournoi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tournoi->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tournoi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tournoi_index', [], Response::HTTP_SEE_OTHER);
    }
}
