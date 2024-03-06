<?php

namespace App\Controller;

use App\Service\SmsGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SmsController extends AbstractController
{
   
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('sms/index.html.twig',['smsSent'=>false]);
    }

    #[Route('/sendSms', name: 'send_sms', methods:'POST')]
    public function sendSms(Request $request, SmsGenerator $smsGenerator): Response
    {
        $number = $request->request->get('number'); // Récupère le numéro de téléphone du destinataire depuis le formulaire
        $name = $request->request->get('name');
        $text = $request->request->get('text');
        

        //Appel du service
        $smsGenerator->sendSms($number, $name, $text); // Utilise le numéro saisi dans le formulaire

        return $this->render('sms/index.html.twig', ['smsSent'=>true]);
    }
}
