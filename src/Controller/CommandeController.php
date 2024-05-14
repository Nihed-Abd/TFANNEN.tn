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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Service\ExcelExporter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\ErrorCorrectionLevel;
use Doctrine\Persistence\ManagerRegistry;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Service\StripeService;
use Stripe\Charge;
use Stripe\Stripe;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/payment', name: 'app_payment1')]
    public function indexp(): Response
    {
        return $this->render('commande/payment.html.twig', [
            'controller_name' => 'PaymentController',
            'stripe_key' => $_ENV["STRIPE_PUBLIC_KEY"],
        ]);
    }
    ///payment
    
    #[Route('/payment/create-charge', name: 'app_stripe_charge2', methods: ['POST'])]
    public function createCharge(Request $request)
    {
        Stripe::setApiKey($_ENV["STRIPE_SECRET_KEY"]);
        Charge::create ([
                "amount" => 5 * 100,
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => "Binaryboxtuts Payment Test"
        ]);
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }  
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
      ///pagination
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository, UserRepository $rep, PaginatorInterface $paginator, Request $request): Response
    {
        
        $user = $rep->find(1);
    
        // Get all commandes
        $commandes = $commandeRepository->findAll();
    
        // Paginate the commandes
        $pagination = $paginator->paginate(
            $commandes,
            $request->query->getInt('page', 1), // Get the current page from the request query parameter 'page'
            4 // Number of items per page
        );
    
        return $this->render('commande/index.html.twig', [
            'commandes' => $pagination,
            'user' => $user,
        ]);
    }
    #[Route('/dash_commande', name: 'app_commande_dash', methods: ['GET'])]
    public function indexdash(CommandeRepository $commandeRepository,Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            // Get the search term from the request
            $searchTerm = $request->query->get('search');
    
            // Call the search function in your repository
            $commandes = $commandeRepository->searchByTerm($searchTerm);

    
            // Convert the results to an array
            if($searchTerm == ""){
                $commandes = $commandeRepository->findAll();;
            }
            $commandesArray = [];
            foreach ($commandes as $commande) {
                $commandesArray[] = [
                    'id' => $commande->getId(),
                    'adresse' => $commande->getAdresse(),
                    'numTel' => $commande->getNumTel(),
                    'prix' => $commande->getPrix(),
                    'produits' => $commande->getProduits(),
                ];
            }

            return new JsonResponse(['commandes' => $commandesArray]);
        }
        return $this->render('commande/liste_commande_dash.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }
    
     
     #[Route('/search', name: 'search', methods: ['GET'])]
      public function search(CommandeRepository $commandeRepository, Request $request, UserRepository $rep): JsonResponse
    {
    $query = $request->query->get('q', ''); // Obtenez le terme de recherche depuis la requête

    // Utilisez votre repository pour effectuer la recherche
    $commandesQuery = $commandeRepository->searchByTerm($query);

    $user = new User();
    $user = $rep->find(1);

    // Renvoyez les résultats en format JSON
    return new JsonResponse([
        'commandes' => $commandesQuery,
        'user' => $user,
    ]);
     }
     
     #[Route('/search_b', name: 'search_b', methods: ['GET'])]
     public function search_b(CommandeRepository $commandeRepository, Request $request, UserRepository $rep): Response
     {
         $query = $request->query->get('q', ''); // Obtain the search term from the request
     
         // Use your repository to perform the search
         $commandesQuery = $commandeRepository->searchByTerm($query);
     
         if ($request->isXmlHttpRequest()) {
             // If the request is AJAX, return only the partial view for the search results
             return $this->render('commande/_search_results.html.twig', [
                 'commandes' => $commandesQuery,
             ]);
         }
     
         // If it's a regular request, render the full page with the search results
         return $this->render('commande/liste_commande_dash.html.twig', [
             'commandes' => $commandesQuery,
         ]);
     }
     
    #[Route('/export', name: 'export_commandes')]
    public function exportCommandesToExcel(ExcelExporter $excelExporter)
    {
        // Get all commandes from the repository
        $commandes = $this->getDoctrine()->getRepository(Commande::class)->findAll();
    
        // Use the ExcelExporter service to save the file
        $filename = $excelExporter->exportCommandes($commandes);
    
        $response = new BinaryFileResponse($filename);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        ));
    
        // Optionally, you can remove the file after download
        register_shutdown_function(function () use ($filename) {
            // Check if the file exists before attempting to unlink
            if (file_exists($filename)) {
                unlink($filename);
            }
        });
    
        return $response;
    }
    //trii

    #[Route('/TriCommandes', name: 'commandes_b')]
    public function TriCommandes(CommandeRepository $repository, Request $request)
    {
        $fieldName = 'prix'; // Set the field to 'prix' for sorting by price
        $commandes = $repository->orderByFieldDESC($fieldName);

        return $this->render("commande/liste_commande_dash.html.twig", [
            "commandes" => $commandes,
        ]);
    }
    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session,UserRepository $rep,ValidatorInterface $validator, MailerInterface $mailer): Response
    {
        $user = new User();
        $user = $rep->find(1);
        $cartData = $session->get('cart', []);
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new Email())
            ->from('haythem.raggad@esprit.tn')
            ->to('haythem.raggad@esprit.tn')
            ->subject('forget-password')
            ->text("looooooooooool")
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
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


    

            return $this->redirectToRoute('app_payment1', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
            'cartData' => $cartData,
            'stripe_key' => $_ENV["STRIPE_PUBLIC_KEY"],
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }
    //qrcode 
    #[Route('/QrCode/{id}', name: 'app_QrCode')]
    public function qrGenerator(ManagerRegistry $doctrine, $id, CommandeRepository $rep)
    {
        $em = $doctrine->getManager();
        $commande = $rep->find($id);
      //  $qrcode = QrCode::create($res->getNom() .  " Et le prix est: " . $res->getPrix())
        $qrcode = QrCode::create( " - L'adresse est :". $commande->getAdresse())

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
