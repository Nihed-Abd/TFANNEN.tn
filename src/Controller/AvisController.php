<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Design;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/avis')]
class AvisController extends AbstractController
{


    ////////////////////////* Client Functions *//////////////////////////
    #[Route('/', name: 'app_avis_index', methods: ['GET'])]
    public function index(AvisRepository $avisRepository): Response
    {
        return $this->render('avis/index.html.twig', [
            'avis' => $avisRepository->findAll(),
        ]);
    }
    #[Route('/new/{design_id}/{user_id}', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $design_id, int $user_id): Response
    {
        $design = $this->getDoctrine()->getRepository(Design::class)->find($design_id);
        
        if (!$design) {
            throw $this->createNotFoundException('Design not found');
        }
        
        $avi = new Avis();
        $avi->setDesign($design);
        
        $form = $this->createForm(AvisType::class, $avi);
        
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($avi);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_design_show', ['design_id' => $design_id, 'users_id' => $user_id], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('avis/ClientVue/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
            'user_id' => $user_id,
        ]);
    }
    
    ///////////////////////* Admin Functions *//////////////////////////
    #[Route('/admin/design/{id}/avis', name: 'admin_design_avis')]
        public function showDesignAvis(int $id, AvisRepository $avisRepository): Response
    {
    $avis = $avisRepository->findBy(['design' => $id]);

    return $this->render('avis/AdminVue/AdminAvis.html.twig', [
        'avis' => $avis,
            ]);
    }

    #[Route('/admin/design/remove/{id}', name: 'admin_avis_delete', methods: ['POST'])]
public function delete(Request $request, Avis $avi, EntityManagerInterface $entityManager): Response
{
    // Check if the CSRF token is valid
    if ($this->isCsrfTokenValid('delete'.$avi->getId(), $request->request->get('_token'))) {
        // Remove the "avis" entity
        $entityManager->remove($avi);
        $entityManager->flush();

        // Get the design ID from the "avi" object
        $designId = $avi->getDesign()->getId();

        // Redirect to the "admin_design_avis" route with the design ID
        return new RedirectResponse($this->generateUrl('admin_design_avis', ['id' => $designId]));
    }
}
 ///////////////////////////////* Designer Functions *////////////////////////////
        #[Route('/designer/{users_id}/{design_id}/avis', name: 'designer_avis_show')]
            public function showDesignerAvis(int $id, AvisRepository $avisRepository): Response
        {
        $avis = $avisRepository->findBy(['design' => $id]);

        return $this->render('avis/designerAvis.html.twig', [
            'avis' => $avis,
                ]);
        }


    #[Route('/{id}', name: 'app_avis_show', methods: ['GET'])]
    public function show(Avis $avi): Response
    {
        return $this->render('avis/show.html.twig', [
            'avi' => $avi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_avis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avis $avi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avis/edit.html.twig', [
            'avi' => $avi,
            'form' => $form,
        ]);
    }

  
}
