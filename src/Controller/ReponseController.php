<?php
namespace App\Controller;
use App\Entity\Reponse;
use App\Entity\Reclamation;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

#[Route('/reponse')]
class ReponseController extends AbstractController
{
    #[Route('/', name: 'app_reponse_index', methods: ['GET'])]
    public function index(Request $request, ReponseRepository $reponseRepository): Response
{
    $searchQuery = $request->query->get('search');

    if ($searchQuery) {
        // Assuming your status and decision fields are properties of the Reponse entity
        $reponses = $reponseRepository->findBySearchQuery($searchQuery);
    } else {
        $reponses = $reponseRepository->findAll();
    }

    return $this->render('reponse/index.html.twig', [
        'reponses' => $reponses,
    ]);
}

    


    #[Route('/new', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reponse);
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(Reponse $reponse): Response
    {
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/respond', name: 'app_reclamation_respond', methods: ['GET', 'POST'])]
    public function respond(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        // Update with your Twilio credentials
        $sid = "ACb9a316043c5f17e215f7b2bfb309bf70";
        $token = "c1d6004530cae120738bf72be12b6e92";

        // Create Twilio client
        $twilio = new Client($sid, $token);

        // Assuming Reponse is your entity for storing responses
        $reponse = new Reponse();
        $reponse->setIdReclamation($reclamation); // Set the reclamation for the response

        // Create form and handle request
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the decision from the form
            $decision = $form->get('decision')->getData();

            // Prepare the message content including the decision
            $messageBody = "Decision: $decision";

            // Send WhatsApp message
            $message = $twilio->messages
                ->create("whatsapp:+21698715915", [
                    "from" => "whatsapp:+14155238886",
                    "body" => $messageBody
                ]);

            // Persist response entity
            $entityManager->persist($reponse);
            $entityManager->flush();

            // Redirect to response index page
            return $this->redirectToRoute('app_reponse_index');
        }

        // Render form template with reclamation and form
        return $this->renderForm('reponse/respond.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }
}
