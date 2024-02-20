<?php

namespace App\Controller;

use App\Entity\Design;
use App\Entity\Avis;
use App\Form\DesignType;
use App\Repository\DesignRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


#[Route('/design')]
class DesignController extends AbstractController
{
    #[Route('/', name: 'app_design_index', methods: ['GET'])]
    public function index(DesignRepository $designRepository): Response
    {
        return $this->render('design/index.html.twig', [
            'designs' => $designRepository->findAll(),
        ]);
    }
    #[Route('/admin', name: 'admin_design_index', methods: ['GET'])]
    public function adminIndex(DesignRepository $designRepository): Response
    {
        $designs = $designRepository->findAll();
        dump($designs); // Check if $design is fetched correctly
        return $this->render('design/designAdmin.html.twig', [
            'designs' => $designs,
        ]);   
    }

   #[Route('/new', name: 'app_design_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $design = new Design();
        $form = $this->createForm(DesignType::class, $design);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form['picture']->getData();
            if ($pictureFile) {
                $filename = md5(uniqid()).'.'.$pictureFile->guessExtension();
                $pictureFile->move(
                    $this->getParameter('pictures_directory'),
                    $filename
                );
                $design->setPicture($filename);
            }

            $entityManager->persist($design);
            $entityManager->flush();

            return $this->redirectToRoute('app_design_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('design/new.html.twig', [
            'design' => $design,
            'form' => $form,
        ]);
    }

    #[Route('/design/{id}', name: 'app_design_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Design $design): Response
    {
        return $this->render('design/show.html.twig', [
            'design' => $design,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_design_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Design $design, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DesignType::class, $design);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_design_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('design/edit.html.twig', [
            'design' => $design,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_design_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Design $design, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$design->getId(), $request->request->get('_token'))) {
            $avisRepository = $entityManager->getRepository(Avis::class);
            $avis = $avisRepository->findBy(['design' => $design]);
            foreach ($avis as $avi) {
                $entityManager->remove($avi);
            }
            $entityManager->remove($design);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_design_index', [], Response::HTTP_SEE_OTHER);
    }

    
   
    

}
