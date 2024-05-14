<?php

namespace App\Controller;

use App\Entity\Promotion;
use App\Form\PromotionType;
use App\Service\TwilioService;
use App\Service\mail;

use App\Repository\PromotionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/promotion')]
class PromotionController extends AbstractController
{
    #[Route('/', name: 'app_promotion_index', methods: ['GET'])]
    public function index(PromotionRepository $promotionRepository): Response
    {
        return $this->render('promotion/index.html.twig', [
            'promotions' => $promotionRepository->findAll(),
        ]);
    }
    //admin
    #[Route('/dash', name: 'app_promotion_dash', methods: ['GET'])]
    public function index1(PromotionRepository $promotionRepository): Response
    {
        return $this->render('promotion/index1.html.twig', [
            'promotions' => $promotionRepository->findAll(),
        ]);
    }

    #[Route('/dash/add', name: 'app_promotion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,mail $mailMessage): Response
    {
        
        $promotion = new Promotion();
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
          /*  $number = '+21692503961'; // Example phone number
            $name = 'Tfannen platforme'; // Example name
            $text = 'promo flash ! new promotion is added to our platform '; // Example text
            $twilioService->sendSms($number, $name, $text);*/
            $to = 'ayedhamza970@gmail.com'; // Define the recipient email address
            $content = 'promo flash ! new promotion is added to our platform'; // Define the email content
            $subject = 'tfannen platform';
            $mailMessage->sendEmail($to, $content, $subject);

            
            $entityManager->persist($promotion);
            $entityManager->flush();

            return $this->redirectToRoute('app_promotion_dash', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderform('promotion/new.html.twig', [
            'promotion' => $promotion,
            'form' => $form,
        ]);
    }

    #[Route('/dash/show/{id}', name: 'app_promotion_show', methods: ['GET'])]
    public function show(Promotion $promotion): Response
    {
        return $this->renderform('promotion/show.html.twig', [
            'promotion' => $promotion,
        ]);
    }

    #[Route('/dash/edit/{id}', name: 'app_promotion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Promotion $promotion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_promotion_dash', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderform('promotion/edit.html.twig', [
            'promotion' => $promotion,
            'form' => $form,
        ]);
    }

    #[Route("/dash/delete/{id}", name:"app_promotion_delete")]
    
public function delete($id, EntityManagerInterface $entityManager): Response
{
    $promotion = $entityManager->getRepository(Promotion::class)->find($id);

    if (!$promotion) {
        $this->addFlash('error', 'promotion not found.');
    } else {
        $entityManager->remove($promotion);
        $entityManager->flush();

        $this->addFlash('success', 'User deleted successfully.');
    }

    return $this->redirectToRoute('app_promotion_dash');
}
   




// Controller action to validate promo code and calculate discounted price
public function checkPromoCode(Request $request): JsonResponse
{
    $promoCode = $request->get('promoCode');
    $designId = $request->get('designId');
    $designPrice = $request->get('designPrice');

    // Perform validation and get the discounted price from your database or logic
    $discountedPrice = $this->getDiscountedPrice($promoCode, $designId, $designPrice);

    // Send the discounted price back to the JavaScript function in the template
    return new JsonResponse(['discountedPrice' => $discountedPrice]);
}

}
