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

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/{user_id}', name: 'app_reclamation_index', methods: ['GET'])]
public function index(ReclamationRepository $reclamationRepository, ReponseRepository $reponseRepository, $user_id): Response
{
    $reclamations = $reclamationRepository->findAllWithResponsesByUserId($user_id);
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
    public function edit(int $user_id , Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', ['user_id' => $reclamation->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
            'user_id' => $user_id,

        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }
    
        // Redirect to the index route with the appropriate parameters
        return $this->redirectToRoute('app_reclamation_index', ['user_id' => $reclamation->getUser()->getId()], Response::HTTP_SEE_OTHER);
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

}
