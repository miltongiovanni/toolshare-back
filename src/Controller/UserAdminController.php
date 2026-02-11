<?php

namespace App\Controller;

use App\Entity\UserAdmin;
use App\Form\UserAdminType;
use App\Repository\UserAdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/admin')]
final class UserAdminController extends AbstractController
{
    #[Route(name: 'app_user_admin_index', methods: ['GET'])]
    public function index(UserAdminRepository $userAdminRepository): Response
    {
        return $this->render('user_admin/index.html.twig', [
            'user_admins' => $userAdminRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userAdmin = new UserAdmin();
        $form = $this->createForm(UserAdminType::class, $userAdmin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userAdmin);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_admin/new.html.twig', [
            'user_admin' => $userAdmin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_admin_show', methods: ['GET'])]
    public function show(UserAdmin $userAdmin): Response
    {
        return $this->render('user_admin/show.html.twig', [
            'user_admin' => $userAdmin,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserAdmin $userAdmin, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserAdminType::class, $userAdmin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_admin/edit.html.twig', [
            'user_admin' => $userAdmin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_admin_delete', methods: ['POST'])]
    public function delete(Request $request, UserAdmin $userAdmin, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userAdmin->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userAdmin);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
