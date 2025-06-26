<?php

namespace App\Controller;

use App\Repository\VacataireRepository;
use App\Entity\Vacataire;
use App\Form\VacataireTypeForm;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class VacataireController extends AbstractController
{
    #[Route('/vacataire', name: 'app_vacataire')]
    public function index(VacataireRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $vacataires = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), 10
        );

        return $this->render('pages/vacataire/index.html.twig', [
            'vacataires' => $vacataires,
        ]);
    }

    #[Route('/vacataire/nouveau','vacataire_new',methods:['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $vacataire = new Vacataire();
        $form = $this->createForm(VacataireTypeForm::class, $vacataire);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vacataire = $form->getData();

            $manager->persist($vacataire);
            $manager->flush();
            $this->addFlash(
                'success',
                'Vos changements ont été enregistrés !'
            );

            return $this->redirectToRoute('app_vacataire');
        }

        return $this->render('pages/vacataire/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/vacataire/edit/{id}', 'vacataire_edit',methods:['GET','POST'])]
    public function edit(Request $request,
        EntityManagerInterface $manager,
        Vacataire $vacataire): Response
    {
        $form = $this->createForm(VacataireTypeForm::class, $vacataire);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $vacataire = $form->getData();
            $manager->flush();
            $this->addFlash(
                'success',
                'Vos changements ont été enregistrés !'
            );

            return $this->redirectToRoute('app_vacataire');
        }

        return $this->render('pages/vacataire/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/vacataire/remove/{id}', 'vacataire_remove',methods:['GET','POST'])]
    public function remove(Request $request,
        EntityManagerInterface $manager,
        Vacataire $vacataire): Response
    {
        $manager->remove($vacataire);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le vacataire a été supprimé !"
        );

        return $this->redirectToRoute('app_vacataire');
    }
}
