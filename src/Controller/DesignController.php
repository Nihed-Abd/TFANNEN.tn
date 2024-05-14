<?php

namespace App\Controller;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\QrCode;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeInterface;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\ValidationException;
use Endroid\QrCode\Label\Font\NotoSans;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry; 

#[Route('/design')]
class DesignController extends AbstractController
{
    //////////////////////////* Visitor Functions *//////////////////////////
  
   //* Design Details *//
   #[Route('/{design_id}', name: 'visitor_design_show', methods: ['GET'], requirements: ['design_id' => '\d+'])]
   public function showVisitor(int $design_id, DesignRepository $designRepository): Response
{
    $design = $designRepository->find($design_id);
    
    if (!$design) {
        throw $this->createNotFoundException('Design not found');
    }
    return $this->render('design/VisitorVue/Visitorshow.html.twig', [
        'design' => $design,
    ]);
}

   #[Route('/search', name: 'search_designs', methods: ['GET'])]
public function searchDesigns(Request $request, DesignRepository $designRepository): JsonResponse
{
    $query = $request->query->get('query');
    $designs = $designRepository->findBySearchQuery($query); // Implement this method in your repository

    // Serialize the designs to JSON format
    $data = [];
    foreach ($designs as $design) {
        $data[] = [
            'id' => $design->getId(),
            'title' => $design->getTitle(),
            'picture' => $design->getPicture(),
            'category' => $design->getCategory(),
        ];
    }

    return new JsonResponse($data);
}

    //* Store *//
    #[Route('/', name: 'Visitor_design_index', methods: ['GET'])]
    public function indexVisitor(Request $request, DesignRepository $designRepository): Response
{
    $currentPage = $request->query->getInt('page', 1);
    $totalDesigns = count($designRepository->findAll());
    $productsPerPage = 6;
    $totalPages = ceil($totalDesigns / $productsPerPage);

    $offset = ($currentPage - 1) * $productsPerPage;
    $designs = $designRepository->findBy([], null, $productsPerPage, $offset);

    return $this->render('design/VisitorVue/VisitorIndex.html.twig', [
        'designs' => $designs,
        'totalDesigns' => $totalDesigns,
        'totalPages' => $totalPages,
        'currentPage' => $currentPage,
        'productsPerPage' => $productsPerPage,
    ]);
}
/////////////////////////// QRCODE //////////////////
#[Route('/QrCode/{id}', name: 'app_QrCode')]
public function qrGenerator(ManagerRegistry $doctrine, $id, DesignRepository $designRepository)
{
    
    $design = $designRepository->find($id);
    
    if (!$design) {
        throw $this->createNotFoundException('Design not found');
    }

    $qrcode = QrCode::create($design->getTitre() .  " Et le prix est: " . $design->getPrix())
        ->setEncoding(new Encoding('UTF-8'))
        ->setSize(300)
        ->setMargin(10)
        ->setForegroundColor(new Color(0, 0, 0))
        ->setBackgroundColor(new Color(255, 255, 255));
    
    $writer = new PngWriter();
    $response = new Response($writer->write($qrcode)->getString(),
        Response::HTTP_OK,
        ['content-type' => 'image/png']
    );
    
    $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'qrcode.png');
    $response->headers->set('Content-Disposition', $disposition);
    
    return $response;
}

    //////////////////////////* Client Functions *///////////////////////////
    #[Route('/client/{user_id}', name: 'app_design_index', methods: ['GET'])]
public function indexClient(Request $request,DesignRepository $designRepository, $user_id): Response
{
    $currentPage = $request->query->getInt('page', 1);
    $totalDesigns = count($designRepository->findAll());
    $productsPerPage = 6;
    $totalPages = ceil($totalDesigns / $productsPerPage);

    $offset = ($currentPage - 1) * $productsPerPage;
    $designs = $designRepository->findBy([], null, $productsPerPage, $offset);
    
    return $this->render('design/ClientVue/index.html.twig', [
        'designs' => $designs,
        'user_id' => $user_id,
        'totalDesigns' => $totalDesigns,
        'totalPages' => $totalPages,
        'currentPage' => $currentPage,
        'productsPerPage' => $productsPerPage,
    ]);
}


    
#[Route('/client/{users_id}/{design_id}', name: 'app_design_show', methods: ['GET'], requirements: ['id' => '\d+'], defaults: ['id' => null])]
public function showClient(int $users_id ,int $design_id, DesignRepository $designRepository): Response
{
    $design = $designRepository->find($design_id);
    
    if (!$design) {
        throw $this->createNotFoundException('Design not found');
    }
    return $this->render('design/ClientVue/show.html.twig', [
        'design' => $design,
        'users_id' => $users_id,
    ]);
}



    ////////////////////////////* Designer Functions *///////////////////////////

            //* Store *//
            #[Route('/designer/{users_id}', name: 'designer_design_index', methods: ['GET'])]
            public function indexDesigner(int $users_id, Request $request, DesignRepository $designRepository): Response
            {
                $currentPage = $request->query->getInt('page', 1);
                $productsPerPage = 6;
                $offset = ($currentPage - 1) * $productsPerPage;
            
                $designs = $designRepository->findBy([], null, $productsPerPage, $offset);
                $totalDesigns = count($designRepository->findAll());
            
                $totalPages = ceil($totalDesigns / $productsPerPage);
            
                return $this->render('design/DesignerVue/designerView.html.twig', [
                    'designs' => $designs,
                    'users_id' => $users_id,
                    'totalDesigns' => $totalDesigns,
                    'totalPages' => $totalPages,
                    'currentPage' => $currentPage,
                    'productsPerPage' => $productsPerPage,
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
                public function newDesign(int $users_id, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
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

    $errors = $validator->validate($design);

    return $this->renderForm('design/DesignerVue/new.html.twig', [
        'design' => $design,
        'form' => $form,
        'users_id' => $users_id,
        'errors' => $errors, 
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


    ////////////////////////* PDF Function *////////////////////////////

    #[Route('/{id}/pdf', name: 'app_design_pdf', methods: ['GET'])]     
    public function AfficheTicketPDF(DesignRepository $repo, $id)
    {
    $pdfoptions = new Options();
    $pdfoptions->set('defaultFont', 'Arial');
    $pdfoptions->setIsRemoteEnabled(true);
    

    $dompdf = new Dompdf($pdfoptions);

    $design = $repo->find($id);

    // Check if the ticket exists
    if (!$design) {
        throw $this->createNotFoundException('Your Design does not exist');
    }

    $html = $this->renderView('design/AdminVue/pdfExport.html.twig', [
        'design' => $design
    ]);

    $html = '<div>' . $html . '</div>';

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A6', 'landscape');
    $dompdf->render();

    $pdfOutput = $dompdf->output();

    return new Response($pdfOutput, Response::HTTP_OK, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="designPDF.pdf"'
    ]);
}

 
    

}
