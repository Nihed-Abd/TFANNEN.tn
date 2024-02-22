<?php

namespace App\Controller;

use App\Entity\Design;
use App\Entity\Avis;
use App\Entity\User;
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
    //////////////////////////* Visitor Functions *//////////////////////////
   
   //* Design Details *//
   #[Route('/{design}', name: 'visitor_design_show', methods: ['GET'], requirements: ['design' => '\d+'])]
   public function showVisitor(Design $design): Response
   {
       return $this->render('design/VisitorVue/VisitorShow.html.twig', [
           'design' => $design,
       ]);
   }
   
    //* Store *//
    #[Route('/', name: 'Visitor_design_index', methods: ['GET'])]
    public function indexVisitor(DesignRepository $designRepository): Response
    {
        return $this->render('design/VisitorVue/VisitorIndex.html.twig', [
            'designs' => $designRepository->findAll(),
        ]);
    }

    //////////////////////////* Client Functions *///////////////////////////
    #[Route('/client/{user_id}', name: 'app_design_index', methods: ['GET'])]
public function indexClient(DesignRepository $designRepository, $user_id): Response
{
    $designs = $designRepository->findAll();
    
    return $this->render('design/ClientVue/index.html.twig', [
        'designs' => $designs,
        'user_id' => $user_id,
    ]);
}


    
#[Route('/client/{user_id}/{design}', name: 'app_design_show', methods: ['GET'], requirements: ['id' => '\d+'], defaults: ['design' => null])]
public function showClient(?Design $design, $user_id): Response
{
    return $this->render('design/ClientVue/show.html.twig', [
        'design' => $design,
        'user_id' => $user_id,
    ]);
}


    ////////////////////////////* Designer Functions *///////////////////////////

            //* Store *//
            #[Route('/designer/{users_id}', name: 'designer_design_index', methods: ['GET'])]
            public function indexDesigner(int $users_id, DesignRepository $designRepository): Response
            {
                $designs = $designRepository->findAll();
                return $this->render('design/DesignerVue/designerView.html.twig', [
                    'designs' => $designs,
                    'users_id' => $users_id, 
                ]);
            }

            //* Own Store //*

            #[Route('/designer/myStore/{users_id}', name: 'app_design_by_designer', methods: ['GET'])]
            public function showStoreDesigner(int $users_id, DesignRepository $designRepository, Request $request): Response
                    {
                                $designs = $designRepository->findBy(['users' => $users_id]);

                        

                                 return $this->render('design/DesignerVue/DesignerProduct.html.twig', [
                                     'designs' => $designs,
                                     'users_id' => $users_id,
                                         ]);
                    }

                //* Add Design *//
                #[Route('/designer/{users_id}/new', name: 'app_design_new', methods: ['GET', 'POST'])]
                public function newDesign(int $users_id, Request $request, EntityManagerInterface $entityManager): Response
                {
                    $design = new Design();
                    $user = $this->getDoctrine()->getRepository(User::class)->find($users_id);
                    if (!$user) {
                        throw $this->createNotFoundException('User not found');
                    }
                    $design->setUsers($user);
                    
                    $form = $this->createForm(DesignType::class, $design);
                    $form->handleRequest($request);
                
                    if ($form->isSubmitted() && $form->isValid()) {
                        $pictureFile = $form['picture']->getData();
                        if ($pictureFile) {
                            $filename = md5(uniqid()) . '.' . $pictureFile->guessExtension();
                            $pictureFile->move(
                                $this->getParameter('pictures_directory'),
                                $filename
                            );
                            $design->setPicture($filename);
                        }
                
                        $entityManager->persist($design);
                        $entityManager->flush();
                
                        return $this->redirectToRoute('app_design_by_designer', ['users_id' => $users_id], Response::HTTP_SEE_OTHER);
                    }
                
                    return $this->renderForm('design/DesignerVue/new.html.twig', [
                        'design' => $design,
                        'form' => $form,
                        'users_id' => $users_id,
                    ]);
                }
                
                
          //* Design Details in store*//
    #[Route('/designer/{users_id}/{design_id}', name: 'designer_design_show')]
    public function showDesignDesigner(int $users_id ,int $design_id, DesignRepository $designRepository): Response
    {
        $design = $designRepository->find($design_id);
    
        if (!$design) {
            throw $this->createNotFoundException('Design not found');
        }
    
        return $this->render('design/DesignerVue/DesignerShopDetails.html.twig', [
            'design' => $design,
            'users_id' => $users_id,

        ]);
    }
            //* store design show *//
    #[Route('/designer/mystore/{users_id}/{design_id}', name: 'designer_ownDesign_show')]
    public function showDesignDetailsOwnStore(int $users_id ,int $design_id, DesignRepository $designRepository): Response
    {
        $design = $designRepository->find($design_id);
    
        if (!$design) {
            throw $this->createNotFoundException('Design not found');
        }
    
        return $this->render('design/DesignerVue/DesignerAvis.html.twig', [
            'design' => $design,
            'users_id' => $users_id,

        ]);
    }
    
                //*edit Design *//
                #[Route('/designer/{users_id}/{design_id}/edit', name: 'app_design_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
public function edit(int $users_id, int $design_id, Request $request, EntityManagerInterface $entityManager, DesignRepository $designRepository): Response
{
    $design = $designRepository->find($design_id);

    if (!$design) {
        throw $this->createNotFoundException('Design not found');
    }

    $form = $this->createForm(DesignType::class, $design);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_design_by_designer', ['users_id' => $users_id], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('design/DesignerVue/edit.html.twig', [
        'users_id' => $users_id,
        'design' => $design,
        'form' => $form,
    ]);
}

                
   //* delete *//
   #[Route('/designer/{users_id}/delete/{design_id}', name: 'designer_design_delete', methods: ['POST'], requirements: ['design_id' => '\d+'])]
public function designerDesignDelete(int $users_id, int $design_id, Request $request, EntityManagerInterface $entityManager): Response
{
    $designRepository = $entityManager->getRepository(Design::class);
    $design = $designRepository->find($design_id);

    if (!$design) {
        throw $this->createNotFoundException('Design not found');
    }

    if ($this->isCsrfTokenValid('delete'.$design->getId(), $request->request->get('_token'))) {
        $avisRepository = $entityManager->getRepository(Avis::class);
        $avis = $avisRepository->findBy(['design' => $design]);
        
        foreach ($avis as $avi) {
            $entityManager->remove($avi);
        }
        
        $entityManager->remove($design);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_design_by_designer', ['users_id' => $users_id], Response::HTTP_SEE_OTHER);
}

     //////////////////////////////* Admin Functions *////////////////////////////////
    

     #[Route('/admin', name: 'admin_design_index', methods: ['GET'])]
     public function adminIndex(DesignRepository $designRepository): Response
     {
         $designs = $designRepository->findAll();
         dump($designs); // Check if $design is fetched correctly
         return $this->render('design/AdminVue/designAdmin.html.twig', [
             'designs' => $designs,
         ]);   
     }
    #[Route('/{id}', name: 'app_design_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function AdminDelete(Request $request, Design $design, EntityManagerInterface $entityManager): Response
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
