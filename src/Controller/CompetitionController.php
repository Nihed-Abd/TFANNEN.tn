<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Form\CompetitionType;
use App\Service\TwilioService;
use App\Entity\Competitiondesigner; 
use App\Form\CompetitiondesignerType;
use App\Repository\CompetitionRepository;
use App\Repository\CompetitiondesignerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\mail;
#[Route('/competition')]
class CompetitionController extends AbstractController
{
    #[Route('/', name: 'app_competition_index', methods: ['GET'])]
    public function index(CompetitionRepository $competitionRepository): Response
    {
        return $this->render('competition/index.html.twig', [
            'competitions' => $competitionRepository->findAll(),
        ]);
    }
    #[Route('/dash', name: 'app_competition_dash', methods: ['GET'])]
    public function index1(CompetitionRepository $competitionRepository): Response
    {
        return $this->render('competition/index1.html.twig', [
            'competitions' => $competitionRepository->findAll(),
        ]);
    }
   
//admin
    #[Route('/dash/add', name: 'app_competition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,mail $mailMessage): Response
    {
        $competition = new Competition();
        $form = $this->createForm(CompetitionType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($competition);
            $entityManager->flush();
          /*  $number = '+21692503961'; // Example phone number
            $name = 'Hamza ayed'; // Example name
            $text = ' Hellodesigner. A new competition has been added to tfanen platform!'; // Example text
            $twilioService->sendSms($number, $name, $text);*/
            $to = 'ayedhamza970@gmail.com'; // Define the recipient email address
            $content = 'promo flash ! new promotion is added to our platform'; // Define the email content
            $subject = 'tfannen platform';
            $mailMessage->sendEmail($to, $content, $subject);
            

            return $this->redirectToRoute('app_competition_dash', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderform('competition/new.html.twig', [
            'competition' => $competition,
            'form' => $form,
        ]);
    }

    #[Route('/dash/show/{id}', name: 'app_competition_show', methods: ['GET'])]
    public function show(Competition $competition): Response
    {
        return $this->render('competition/show.html.twig', [
            'competition' => $competition,
        ]);
    }

    #[Route('/dash/edit/{id}', name: 'app_competition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Competition $competition, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompetitionType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_competition_dash', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderform('competition/edit.html.twig', [
            'competition' => $competition,
            'form' => $form,
        ]);
    }
    #[Route('/dash/delete/{id}', name: 'app_competition_delete', methods: ['POST'])]
    public function delete(Request $request, Competition $competition, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$competition->getId(), $request->request->get('_token'))) {
            $entityManager->remove($competition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_competition_dash', [], Response::HTTP_SEE_OTHER);
    }
    //participation 
    #[Route('/dash/participate', name: 'app_participating_dash', methods: ['GET'])]
    public function index2(CompetitiondesignerRepository $competitiondesignerRepository): Response
    {
        return $this->render('competition/index2.html.twig', [
            'competitiondesigners' => $competitiondesignerRepository->findAll(),
        ]);
    }
    #[Route("/participate", name:"competition_participate")]
    public function participateCompetition(Request $request, EntityManagerInterface $entityManager): Response
    {
        $competitiondesigner = new Competitiondesigner(); // Create a new instance of Competitiondesigner
        $form = $this->createForm(CompetitiondesignerType::class, $competitiondesigner);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($competitiondesigner); // Persist the participant, not $competition
            $entityManager->flush();
    
            return $this->redirectToRoute('app_participating_dash', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('competition/participate.html.twig', [
            'competitiondesigner' => $competitiondesigner,
            'f' => $form->createView(),
        ]);
    }

  
}
