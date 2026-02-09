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

            return $this->redirectToRoute('app_equipe_created', ['id' => $equipe->getId()]);
        }

        return $this->render('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id<\d+>}', name: 'app_equipe_delete', methods: ['POST'])]
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

    #[Route('/{id<\d+>}/join', name: 'app_equipe_join', methods: ['GET'])]
    public function join(Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$equipe->getMembers()->contains($user)) {
            $equipe->addMember($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipe_index', ['id' => $equipe->getId()]);
    }

    #[Route('/{id<\d+>}/json', name: 'app_equipe_json', methods: ['GET'])]
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
    #[Route('/{id<\d+>}/created', name: 'app_equipe_created', methods: ['GET'])]
    public function created($id, EntityManagerInterface $entityManager): Response
    {
        $equipe = $entityManager->getRepository(Equipe::class)->find($id);
        if (!$equipe) {
            return $this->redirectToRoute('app_equipe_index');
        }

        // Render the "team created" onboarding page
        return $this->render('equipe/created.html.twig', [
            'team' => $equipe,
        ]);
    }
    #[Route('/{id<\d+>}/dashboard', name: 'app_equipe_dashboard', methods: ['GET'])]
    public function dashboard($id, EntityManagerInterface $entityManager): Response
    {
        $equipe = $entityManager->getRepository(Equipe::class)->find($id);
        if (!$equipe) {
            return $this->redirectToRoute('app_equipe_index');
        }
        $user = $this->getUser();

        // Ensure requesting user is a member
        $isMember = false;
        if ($user) {
            $isMember = $equipe->getMembers()->contains($user);
        }

        if (!$isMember) {
            // Owner is implicitly a member, double-check
            if ($equipe->getOwner() !== $user) {
                throw $this->createAccessDeniedException();
            }
        }

        $role = ($equipe->getOwner() === $user) ? 'LEADER' : 'MEMBER';
        $membership = ['role' => $role];

        return $this->render('equipe/dashboard.html.twig', [
            'team' => $equipe,
            'membership' => $membership,
        ]);
    }
    #[Route('/{id<\d+>}/invite', name: 'app_equipe_invite', methods: ['GET','POST'])]
    public function invite($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = $entityManager->getRepository(Equipe::class)->find($id);
        if (!$equipe) {
            return $this->redirectToRoute('app_equipe_index');
        }

        // Placeholder invitations list (replace with real invite entity/service later)
        $invitations = [
            ['email' => 'rajhiaziz@gmail.com', 'status' => 'pending'],
            ['email' => 'player2@gmail.com', 'status' => 'accepted'],
        ];

        // If form submitted (simple demo), you could handle sending invite here.
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            if ($email) {
                // Persist invite or send email (not implemented)
                $invitations[] = ['email' => $email, 'status' => 'pending'];
            }
        }

        return $this->render('equipe/invite.html.twig', [
            'equipe' => $equipe,
            'invitations' => $invitations,
        ]);
    }
    #[Route('/my-teams', name: 'app_my_teams', methods: ['GET'])]
    public function myTeams(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $ownedTeams = $entityManager->getRepository(Equipe::class)->findBy(['owner' => $user]);

        $qb = $entityManager->createQueryBuilder();
        $qb->select('e')
            ->from(Equipe::class, 'e')
            ->join('e.members', 'm')
            ->where('m = :user')
            ->setParameter('user', $user);
        $joinedTeams = $qb->getQuery()->getResult();

        $total = count($ownedTeams) + count($joinedTeams);

        return $this->render('equipe/my-teams.html.twig', [
            'ownedTeams' => $ownedTeams,
            'joinedTeams' => $joinedTeams,
            'totalTeams' => $total,
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
