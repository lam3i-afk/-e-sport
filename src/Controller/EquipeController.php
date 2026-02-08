<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\User;

use App\Form\Equipe1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/equipe')]
final class EquipeController extends AbstractController
{
    #[Route(name: 'app_equipe_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('equipe/index.html.twig');
    }

    #[Route('/equipes', name: 'get_equipe', methods: ['GET'])]
    public function get(EntityManagerInterface $entityManager): Response
    {
        $equipes = $entityManager->getRepository(Equipe::class)->findAll();

        return $this->render('equipe/afficher.html.twig', [
            'equipes' => $equipes,
        ]);
    }

    #[Route('/new', name: 'app_equipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(Equipe1Type::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $equipe->setOwner($user);

            // owner devient membre automatiquement
            $equipe->addMember($user);

            $entityManager->persist($equipe);
            $entityManager->flush();

            return $this->redirectToRoute('app_equipe_index');
        }

        return $this->render('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        // seul owner peut modifier
        if ($this->getUser() !== $equipe->getOwner()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(Equipe1Type::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_equipe_index');
        }

        return $this->render('equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipe_delete', methods: ['POST'])]
    public function delete(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        // seul owner peut supprimer
        if ($this->getUser() !== $equipe->getOwner()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$equipe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipe_index');
    }

    #[Route('/{id}/join', name: 'app_equipe_join', methods: ['GET'])]
    public function join(Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$equipe->getMembers()->contains($user)) {
            $equipe->addMember($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipe_index', ['id' => $equipe->getId()]);
    }

    #[Route('/{id}/json', name: 'app_equipe_json', methods: ['GET'])]
    public function getJson(Equipe $equipe): JsonResponse
    {
        $tournaments = [];
        foreach ($equipe->getTournois() as $tournament) {
            $tournaments[] = $tournament->getNom();
        }

        return $this->json([
            'id' => $equipe->getId(),
            'nom' => $equipe->getNom(),
            'owner' => $equipe->getOwner()?->getUserIdentifier(),
            'tournaments' => $tournaments,
            'memberCount' => $equipe->getMembers()->count(),
            'maxMembers' => $equipe->getMaxMembers(),

        ]);
    }
    #[Route('/user/{id}/owner', name: 'app_equipe_by_owner', methods: ['GET'])]
public function getEquipesByOwner(
    User $user,
    EntityManagerInterface $entityManager
): Response
{
    $equipes = $entityManager->getRepository(Equipe::class)
        ->findBy(['owner' => $user]);

    return $this->render('equipe/afficher.html.twig', [
        'equipes' => $equipes,
    ]);
}
#[Route('/user/{id}/not-member', name: 'app_equipe_not_member', methods: ['GET'])]
public function getEquipesNotMember(
    User $user,
    EntityManagerInterface $entityManager
): Response
{
    $qb = $entityManager->createQueryBuilder();

    $qb->select('e')
        ->from(Equipe::class, 'e')
        ->where('e.owner != :user OR e.owner IS NULL')
        ->andWhere(
            $qb->expr()->not(
                $qb->expr()->exists(
                    $entityManager->createQueryBuilder()
                        ->select('m2.id')
                        ->from(Equipe::class, 'e2')
                        ->join('e2.members', 'm2')
                        ->where('e2 = e')
                        ->andWhere('m2 = :user')
                        ->getDQL()
                )
            )
        )
        ->setParameter('user', $user);

    return $this->render('equipe/afficher.html.twig', [
        'equipes' => $qb->getQuery()->getResult(),
    ]);
}

}
