<?php

namespace App\Controller;
use App\Entity\User;
use App\Service\JwtService;
use App\Service\SendMailService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGeneratorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\EmailGenerator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Service\SendMailServ;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Carriere;
use App\Form\UserType;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Finder\Finder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormError;
use App\Mailer\MyAuthCodeMailer;
use App\Mailer\AuthCodeMailerInterface;
use App\Service\JwtServicee;


class UserController extends AbstractController
{
   
    

    #[Route('/get_images', name: 'get_images')]

    public function getImages(): JsonResponse
    {
        $directory = $this->getParameter('kernel.project_dir') . '/public/assets/images/avatars/';

        $images = array_diff(scandir($directory), ['..', '.']);
        $images = array_values(array_diff($images, ['.', '..'])); // Reindex the array
    
        return new JsonResponse($images);
    }
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,MailerInterface $mailer,CodeGeneratorInterface $codeGenerator, SendMailServ $mail, JwtServicee $jwt ): Response
    {
        $form = $this->createForm(UserType::class);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
     // Check for duplicate email before persisting
     $existingUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

     if ($existingUser) {
         // Add a custom error to the form
         $form->get('email')->addError(new FormError('This email is already taken. Please choose another one.'));

         // You may want to return here to prevent further processing
         return $this->render('user/register.html.twig', [
             'form' => $form->createView(),
         ]);
     }
            // Set the default role for the user
            $user->setRoles(['ROLE_CLIENT']);
    
            // Get the selected image name from the hidden field
            $pictureFile = $form->get('picture')->getData();

            if ($pictureFile) {
                $newFilename = uniqid() . '.' . $pictureFile->guessExtension();

                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                }   
                

                $user->setPicture($newFilename); // Set the file path in the user entity
            }
    
            // Encode and set the password
            $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);
    
            $authCode = $codeGenerator->generateAndSend($user);

        // Check if the authentication code was generated successfully

        $header = [
            'typ' => 'JWT',
            'alg' => 'HS25'
        ];

        $payload = [
            'user_id' => $user->getId()
        ];

        $token= $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        $mail->send(
            'ilyess.saoudi@gmail.com',
            $user->getEmail(),
            'Your Account Activiation',
            'registerEm',
            compact('user' ,'token')
        );
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('base_admin');
        
        
        }
    
        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/verif/{token}',name:'verify_user')]
    public function verifyUser($token, JwtServicee $jwt, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
                if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret')))
                {
                    $payload= $jwt->getPayload($token);

                    $user= $userRepository->find($payload['user_id']);

                    if ($user && !$user->getIsVerified()){
                            
                        $user->setIsVerified(true);
                        $em->persist($user);
                        $em->flush();
                        $roles = $user->getRoles();

                        if (in_array('ROLE_CLIENT', $roles, true)) {
                            $this->addFlash('success', 'Client is activated');
                            return $this->redirectToRoute('base_client');
                        } elseif (in_array('ROLE_DESIGNER', $roles, true)) {
                            $this->addFlash('success', 'Designer is activated');
                            return $this->redirectToRoute('base_designer');
                        }
                    }
                }

                $this->addFlash('danger', 'The token is invalid or expired');
                return $this->redirectToRoute('app_login');

    }
       
    #[Route('/listUsers', name: 'list_all_users', methods: ['GET'])]
public function displayUsersForm(EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer): Response
{
    $usersQueryBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('u')
        ->where('u.roles LIKE :roleClient OR u.roles LIKE :roleDesigner')
        ->setParameter('roleClient', '%"ROLE_CLIENT"%')
        ->setParameter('roleDesigner', '%"ROLE_DESIGNER"%');

    // Handle search keyword
    $searchKeyword = $request->query->get('keyword');
    if ($searchKeyword) {
        $usersQueryBuilder
            ->andWhere('u.username LIKE :searchKeyword OR u.email LIKE :searchKeyword')
            ->setParameter('searchKeyword', '%' . $searchKeyword . '%');
    }

    // Handle AJAX request with filtering
    if ($request->isXmlHttpRequest()) {
        $users = $usersQueryBuilder->getQuery()->getResult();
        try {
            $jsonUsers = $serializer->serialize($users, 'json');
            return new JsonResponse($jsonUsers, 200, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    // Render full page for non-AJAX requests
    $users = $usersQueryBuilder->getQuery()->getResult();
    return $this->render('user/List_Users.html.twig', [
        'users' => $users,
    ]);
}

#[Route("/listUsers/delete/{id}", name:"delete_user")]
    
public function deleteUser($id, EntityManagerInterface $entityManager): Response
{
    $user = $entityManager->getRepository(User::class)->find($id);

    if (!$user) {
        $this->addFlash('error', 'User not found.');
    } else {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'User deleted successfully.');
    }

    return $this->redirectToRoute('list_all_users');
}
#[Route("/editAdminProfile", name: "edit_admin")]
public function editAdminProfile(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();

    $form = $this->createFormBuilder($user)
        ->add('username', TextType::class, [
            'label' => 'User Name',
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => ['id' => 'emailInput'],
            'required' => true,
            'constraints' => [
                new Regex([
                    'pattern' => '/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                    'message' => 'The email "{{ value }}" is not a valid email.',
                ]),
            ],
        ])
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => false, // Allow empty password
            'first_options'  => ['label' => 'Enter New Password'],
            'second_options' => ['label' => 'Confirm Password'],
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Only encode and set the password if it's not empty
        $password = $form->get('password')->getData();
        if ($password !== null && !empty($password)) {
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
        }

        // Persist changes to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('base_admin');
    }

    return $this->render('user/AdminProfile.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route("/editUserProfile", name: "edit_user")]
public function editUserProfile(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();

    $form = $this->createFormBuilder($user)
        ->add('username', TextType::class, [
            'label' => 'User Name',
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => ['id' => 'emailInput'],
            'required' => true,
            'constraints' => [
                new Regex([
                    'pattern' => '/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                    'message' => 'The email "{{ value }}" is not a valid email.',
                ]),
            ],
        ])
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => false, // Allow empty password
            'first_options'  => ['label' => 'Enter New Password'],
            'second_options' => ['label' => 'Confirm Password'],
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Only encode and set the password if it's not empty
        $password = $form->get('password')->getData();
        if ($password !== null && !empty($password)) {
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
        }

        // Persist changes to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('base_client');
    }

    return $this->render('user/UserProfile.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route("/editDesignerProfile", name: "edit_designer")]
public function editDesignerProfile(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();

    $form = $this->createFormBuilder($user)
        ->add('username', TextType::class, [
            'label' => 'User Name',
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => ['id' => 'emailInput'],
            'required' => true,
            'constraints' => [
                new Regex([
                    'pattern' => '/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                    'message' => 'The email "{{ value }}" is not a valid email.',
                ]),
            ],
        ])
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'The password fields must match.',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => false, // Allow empty password
            'first_options'  => ['label' => 'Enter New Password'],
            'second_options' => ['label' => 'Confirm Password'],
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Only encode and set the password if it's not empty
        $password = $form->get('password')->getData();
        if ($password !== null && !empty($password)) {
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encodedPassword);
        }

        // Persist changes to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('base_designer');
    }

    return $this->render('user/DesignerProfile.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/ListOfSubmissions', name: 'list_all_submissions')]
public function dashboard(): Response
{
    // Fetch submissions to become a designer for users with the role 'ROLE_USER'
    $submissions = $this->getDoctrine()->getRepository(Carriere::class)->createQueryBuilder('c')
        ->join('c.user', 'u')
        ->where('u.roles LIKE :role')
        ->setParameter('role', '%ROLE_CLIENT%')
        ->getQuery()
        ->getResult();

    return $this->render('user/ListAllSubmissions.html.twig', [
        'submissions' => $submissions,
    ]);
}

#[Route('/admin/accept-submission/{id}', name: 'admin_accept_submission')]
public function acceptSubmission(int $id): Response
{
    $entityManager = $this->getDoctrine()->getManager();

    // Fetch Carriere entity by ID
    $submission = $entityManager->getRepository(Carriere::class)->find($id);

    if (!$submission) {
        throw $this->createNotFoundException('Submission not found');
    }

    // Update user role to designer
    $user = $submission->getUser();
    $user->setRoles(['ROLE_DESIGNER']);

    // Remove user from Carriere table
    $entityManager->remove($submission);

    // Flush changes to apply removal
    $entityManager->flush();

    // Persist changes to update user role
    $entityManager->persist($user);
    $entityManager->flush();

    $this->addFlash('success', 'Submission accepted. User is now a designer.');

    return $this->redirectToRoute('list_all_submissions');
}
#[Route('/admin/reject-submission/{id}', name: 'admin_reject_submission')]
public function rejectSubmission(int $id): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    
    // Fetch Carriere entity by ID
    $submission = $entityManager->getRepository(Carriere::class)->find($id);

    if (!$submission) {
        throw $this->createNotFoundException('Submission not found');
    }

    // Retrieve the associated User
    $user = $submission->getUser();

    // Remove the Carriere entity
    $entityManager->remove($submission);

    // Persist changes to update user (move this line before flushing)
    $entityManager->persist($user);

    // Flush changes
    $entityManager->flush();

    $this->addFlash('danger', 'Submission rejected. User remains unchanged.');

    return $this->redirectToRoute('list_all_submissions');
}

  
      #[Route('/enable-two-factor', name: 'enable_two_factor')]
     
    public function enableTwoFactor(GoogleAuthenticatorInterface $googleAuthenticator): Response
    {
        // Fetch the current user (you may need to adjust this based on your authentication setup)
        $user = $this->getUser();

        // Generate Google Authenticator secret
        $secret = $googleAuthenticator->generateSecret();

        // Persist $secret to the user entity
        //$user->setGoogleAuthenticatorSecret($secret);

        // Your other logic to update the user entity

        return $this->redirectToRoute('user_settings'); // Redirect back to user settings
    }

//#[Route('/search-users', name: 'search_users', methods: ['GET'])]

/*public function searchUsers(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $searchTerm = $request->query->get('q');
    
    // Use your repository method to search users based on $searchTerm
    $users = $entityManager->getRepository(User::class)->findBySearchTerm($searchTerm);

    $userArray = [];
    foreach ($users as $user) {
        $userArray[] = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            // Add other fields as needed
        ];
    }

    return new JsonResponse($userArray);
}*/
/**
     * @Route("/reset-password", name="reset_password")
     */
   /* public function RestPassword(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('ilyess.saoudi@gmail.com')
            ->to('ilyess.saoudi@gmail.com')
            ->subject('Test Email')
            ->text('This is a test email.');

        $mailer->send($email);

        return new Response('Test email sent!');
    }


    #[Route('/email_verif', name: 'email_verification')]

    public function EmailV(MailerInterface $mailer, htmlTemplate $template ): Response

    {
        $email= (new Email())
            ->form('ilyess.saoudi@gmail.com')
            ->to(app.user.email)
            ->subject('Email Verification')
            ->text('We would appreciate if you verify your Email')
            ->htmlTemlplate("email/$template.html.twig");
    }
    */
}
