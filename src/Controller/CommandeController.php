<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\User;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/{id}/delete',name: 'app_commande_delete_f', methods:['POST','GET'])]
    public function deletef(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
            $entityManager->remove($commande);
            $entityManager->flush();

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/deleteB',name :'app_commande_delete_b',methods: ['POST','GET'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
            $entityManager->remove($commande);
            $entityManager->flush();

        return $this->redirectToRoute('app_commande_dash', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/add_to_cart', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(Request $request, SessionInterface $session): Response
    {
        $titre = $request->request->get('titre');
        $prix = $request->request->get('prix');

        $cart = $session->get('cart', []);

        $cart[] = [
            'titre' => $titre,
            'prix' => $prix,
        ];

        $session->set('cart', $cart);

        return new Response('Item added to cart successfully', Response::HTTP_OK);
    }

    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository,UserRepository $rep): Response
    {
        $user = new User();
        $user = $rep->find(1);
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
            'user' => $user,
        ]);
    }
    #[Route('/dash_commande', name: 'app_commande_dash', methods: ['GET'])]
    public function indexdash(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/liste_commande_dash.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session,UserRepository $rep,ValidatorInterface $validator): Response
    {
        $user = new User();
        $user = $rep->find(1);
        $cartData = $session->get('cart', []);
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setUser($user);
            $errors = $validator->validate($commande);
            $entityManager->persist($commande);
            $entityManager->flush();
            $session->clear();
            if (count($errors) > 0) {
                // Handle validation errors
                return $this->renderForm('commande/new.html.twig', [
                    'commande' => $commande,
                    'form' => $form,
                    'cartData' => $cartData,
                    'errors' => $errors,
                ]);
            }
    
            $entityManager->persist($commande);
            $entityManager->flush();
            $session->clear();
    

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
            'cartData' => $cartData,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Continue with your logic here
            // ...

            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }



}
