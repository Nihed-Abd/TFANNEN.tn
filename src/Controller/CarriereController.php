<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Carriere;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class CarriereController extends AbstractController
{
    #[Route('/become_designer', name: 'become-designer')]

    public function submitBecomeDesignerForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new instance of Carriere
        $carriere = new Carriere();

        // Create and handle the form
        $form = $this->createFormBuilder($carriere)
            ->add('cv', FileType::class, [
                'label' => 'Upload your CV',
                'required' => true,
                'mapped' => false, // Since we're not directly binding to the entity
            ])
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload if needed
            $file = $form['cv']->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('cv_directory'), $fileName); // Define cv_directory in your services.yaml

            // Set the file path in the Carriere entity
            $carriere->setCv($fileName);
            // Associate the User object with the Carriere entity
            $carriere->setUser($this->getUser());

              // Update the user's role to "designer"

              // Persist changes to the database
              $entityManager->persist($carriere);
              $entityManager->flush();

            // Optionally, add a flash message or other logic
            $this->addFlash('success', 'Your become designer request has been submitted.');

            return $this->redirectToRoute('base_client'); // Replace with your actual route
        }

        return $this->render('user/becomeDesigner.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
