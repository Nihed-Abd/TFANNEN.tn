<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\User;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;
use Knp\Component\Pager\PaginatorInterface;
use TCPDF;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/{user_id}', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository, ReponseRepository $reponseRepository, PaginatorInterface $paginator, Request $request, $user_id): Response
    {
        $query = $reclamationRepository->findAllWithResponsesByUserId($user_id); // Get the query instead of directly fetching results
    
        // Paginate the results
        $reclamations = $paginator->paginate(
            $query, // Query to paginate
            $request->query->getInt('page', 1), // Current page number
            3 // Number of items per page
        );
    
        $form = $this->createForm(ReclamationType::class); // Create the form here
    
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
            'user_id' => $user_id,
            'form' => $form->createView(), // Pass the form variable to the template
        ]);
    }


  #[Route('/new/{user_id}', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, $user_id): Response
{
    // Retrieve the User object by user_id
    $user = $entityManager->getRepository(User::class)->find($user_id);
    
    if (!$user) {
        throw $this->createNotFoundException('User not found');
    }

    $reclamation = new Reclamation();
    $reclamation->setDate(new \DateTime());
    $reclamation->setUser($user); // Associate the User object with the reclamation

    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reclamation);
        $entityManager->flush();

        // Send SMS using Twilio
      /*  $sid    = "ACb9a316043c5f17e215f7b2bfb309bf70"; // Your Twilio Account SID
        $token  = "c1d6004530cae120738bf72be12b6e92"; // Your Twilio Auth Token
        $twilio = new \Twilio\Rest\Client($sid, $token);


        $to = "+21698715915"; // Recipient's phone number
        $from = "+12315254787"; // Your Twilio phone number
        $body = "New complaint uploaded: {$reclamation->getObjet()}"; // Body of the SMS

        $message = $twilio->messages->create($to, [
            "from" => $from,
            "body" => $body
        ]);*/

        // Redirect to the index route for the specific user
        return $this->redirectToRoute('app_reclamation_index', ['user_id' => $user_id], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('reclamation/new.html.twig', [
        'reclamation' => $reclamation,
        'form' => $form,
        'user_id' => $user_id,
    ]);
}

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        $response = $reclamation->getReponse();
    
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
            'response' => $response,
        ]);
    }

    #[Route('{user_id}/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(int $user_id ,int $id, Request $request,  EntityManagerInterface $entityManager,ReclamationRepository $r): Response
    {

        $reclamation = $r->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }
    
       
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->flush();
            
        return $this->redirectToRoute('app_reclamation_index', ['user_id' => $user_id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
            'user_id' => $user_id,

        ]);
    }

    #[Route('/{id}/{user_id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager, int $user_id): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }
    
        // Redirect to the index route with the appropriate parameters
        return $this->redirectToRoute('app_reclamation_index', ['user_id' => $user_id], Response::HTTP_SEE_OTHER);
    }
    

    #[Route('/reclamations', name: 'app_reclamation_list', methods: ['GET'])]
    public function listReclamations(Request $request, ReponseRepository $reponseRepository, ReclamationRepository $reclamationRepository): Response
    {
        $searchQuery = $request->query->get('search');
    
        if ($searchQuery) {
            // Assuming your object and type fields are properties of the Reclamation entity
            $reclamations = $reclamationRepository->findByObjectOrType($searchQuery, $searchQuery);
        } else {
            $reclamations = $reclamationRepository->findAll();
        }
    
        return $this->render('reponse/list.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    public function generatePdf(int $reclamationId, ContainerInterface $container): Response
{
    // Retrieve the reclamation by ID
    $entityManager = $container->get('doctrine')->getManager();
    $reclamation = $entityManager->getRepository(Reclamation::class)->find($reclamationId);

    if (!$reclamation) {
        throw $this->createNotFoundException('Reclamation not found');
    }

    // Create a new TCPDF instance
    $pdf = new TCPDF();

    // Set document information
    $pdf->SetCreator('YourAppName');
    $pdf->SetAuthor('YourName');
    $pdf->SetTitle('Reclamation PDF');

    // Add a page
    $pdf->AddPage();

    // Include your logo and design elements
    //$pdf->Image('path/to/your/logo.png', 10, 10, 50, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

    // Add reclamation details to the PDF
    $html = $this->renderView('pdf_template.html.twig', ['reclamation' => $reclamation]);

    // Render HTML template as PDF
    $pdf->writeHTML($html);

    // Output the PDF as a response
    return new Response($pdf->Output('reclamation.pdf', 'I'), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="reclamation.pdf"',
    ]);
}

}
