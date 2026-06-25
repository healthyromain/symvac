<?php

namespace App\Controller;

use App\Entity\Vacataire;
use App\Form\VacataireType;
use App\Repository\VacataireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VacataireController extends AbstractController
{
    #[Route('/vacataire', name: 'app_vacataire')]
    public function index(
        VacataireRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $vacataires = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/vacataire/index.html.twig', [
            'vacataires' => $vacataires,
        ]);
    }

    #[Route('/vacataire/nouveau', name: 'vacataire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vacataire = new Vacataire();

        $form = $this->createForm(VacataireType::class, $vacataire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($vacataire);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Vos changements ont été enregistrés !'
            );

            return $this->redirectToRoute('app_vacataire');
        }

        return $this->render('pages/vacataire/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/vacataire/{id}/modifier', name: 'vacataire_edit', methods: ['GET', 'POST'])]
    public function edit(
        Vacataire $vacataire,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(VacataireType::class, $vacataire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le vacataire a bien été modifié !'
            );

            return $this->redirectToRoute('app_vacataire');
        }

        return $this->render('pages/vacataire/edit.html.twig', [
            'form' => $form->createView(),
            'vacataire' => $vacataire,
        ]);
    }

    #[Route('/vacataire/{id}/supprimer', name: 'vacataire_delete', methods: ['POST'])]
    public function delete(
        Vacataire $vacataire,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->remove($vacataire);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le vacataire a bien été supprimé !'
        );

        return $this->redirectToRoute('app_vacataire');
    }
}