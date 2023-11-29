<?php

namespace App\Controller;




use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DistributedTicketsRepository;
use App\Repository\TicketsRepository;
use App\Repository\GameRepository;
use App\Repository\WinnersRepository;
use App\Repository\UserRepository;
// Assurez-vous que le chemin est correct
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Form\UserType;
use App\Entity\Winners;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User; 
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;




class AdminController extends AbstractController
{
           
    protected $em;
    protected $distributedTicketsRepository;
    protected $ticketsRepository;
    protected $gameRepository;
    protected $winnersRepository;
    protected $userRepository;

    public function __construct(EntityManagerInterface $em,UserRepository $userRepository, WinnersRepository $winnersRepository, DistributedTicketsRepository $distributedTicketsRepository, TicketsRepository $ticketsRepository, GameRepository $gameRepository){
        $this->em = $em;
        $this->distributedTicketsRepository = $distributedTicketsRepository;
        $this->ticketsRepository = $ticketsRepository;
        $this->gameRepository = $gameRepository;
        $this->winnersRepository = $winnersRepository;
        $this->userRepository = $userRepository;
    }

    

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin", name="app_admin")
     */
    public function admin(Request $request): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login', []);  
        }
        $ticketdis = $this->distributedTicketsRepository->findAll();
        $ticketwin = $this->winnersRepository->findAll();
        $tickets = $this->ticketsRepository->findAll();
        $aboutUs = $this->userRepository->findByAboutUs();
        $city = $this->userRepository->findByCity();
        $newsLetterCustomer = $this->userRepository->findBy(['newsletter' => 1]);
        return $this->render('admin/index.html.twig', [
            'ticketsdis' => $ticketdis,
            'ticketwin' => $ticketwin,
            'tickets' => $tickets,
            'aboutUs' =>$aboutUs,
            'cities' =>$city,
            'newsLetterCustomerNB' =>count($newsLetterCustomer),
            'newsLetterCustomers' =>$newsLetterCustomer,
        ]);
    }


    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/users", name="admin_users_list")
     */
    public function userList(UserRepository $userRepository): Response
    {
        $allUsers = $userRepository->findAll();
        
        // Filtrer les utilisateurs avec le rôle "user" et exclure les autres rôles
        $users = array_filter($allUsers, function($user) {
            $roles = $user->getRoles();
            return in_array('ROLE_USER', $roles) && !in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_EMPLOYE', $roles);
        });

        return $this->render('admin/list.html.twig', [
            'users' => $users,
        ]);
    }
    
    /**      
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/employee", name="admin_employee_list")
     */
    public function employeeList(UserRepository $userRepository): Response
    {
        $users = $userRepository->findStrictlyEmployees();


        return $this->render('employe/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/users/new", name="admin_user_new", methods={"GET", "POST"})
     */
    public function addUser(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'exclude_password' => false, // Exclure le champ plainPassword lors de l'ajout d'un nouvel utilisateur
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);

            // Set other properties
            // ...

            // Persist the new user entity
            $entityManager->persist($user);
            $entityManager->flush();

            // Add a success message and redirect to the user list
            $this->addFlash('success', 'New user added successfully.');
            return $this->redirectToRoute('admin_users_list');
        }

        // Render the form to create a new user
        return $this->render('admin/new.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }




    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/users/delete/{id}", name="admin_user_delete", methods={"POST"})
     */
    public function deleteUser(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager, $id): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }

        // Check CSRF token
        $token = new CsrfToken('delete'.$id, $request->request->get('_token'));
        if ($csrfTokenManager->isTokenValid($token)) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'User deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('admin_users_list');
    }

    /**
     * @Route("/admin/users/{id}/edit", name="admin_user_edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editUser(Request $request, User $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If you have a plainPassword in your form, you'd handle the password encoding here
            // Only encode if the plainPassword is not null or empty
            if (!empty($form->get('plainPassword')->getData())) {
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user, 
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully.');

            return $this->redirectToRoute('admin_users_list');
        }

        return $this->render('admin/edit.html.twig', [
            'userForm' => $form->createView(),
            'user' => $user, // Pass the user to the template if needed for context
        ]);
    }


    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/sendNewsletter", name="app_admin_send_newsletter")
     */
    public function sendNewsletter(Request $request): Response
    {
        $newsletterTitle = $request->request->get('newsletterTitle');
        $newsletter = $request->request->get('newsletter');

        return $this->redirectToRoute('app_login');
    }

    


        /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/refait", name="refait")
     */
    public function refait(Request $request,EntityManagerInterface $em): Response
    {   $game = $this->gameRepository->find(1);
        $game->setClosingDate(new \Datetime());
        $this->em->persist($game);
        $this->em->flush();
   

        return $this->redirectToRoute('app_admin');
    }

    
            /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/groswin", name="gros_win")
     */
    public function groswinAction(Request $request,EntityManagerInterface $em): Response{
        $t = $this->ticketsRepository->find(6);
        $game = $this->gameRepository->find(1);
        $date = $game->getClosingDate();       
        $wingros = $this->winnersRepository->findby(['ticket' => $t]);
        if ( !$wingros){
        $wins = $this->winnersRepository->findAll();
        $rand = rand(0,count($wins)-1);
        $w = $wins[$rand];
        $win = new Winners();
        $win->setUser($w->getUser());
        $win->setSend(false);
        $win->setTicket($t);
        $this->em->persist($win);
        $this->em->flush();
    }
            
        return $this->redirectToRoute('gros_lot');
    }


        /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/groslot", name="gros_lot")
     */
    public function groslotAction(Request $request,EntityManagerInterface $em): Response{
        $t = $this->ticketsRepository->find(6);
        $game = $this->gameRepository->find(1);
        $date = $game->getClosingDate();       
        $wingros = $this->winnersRepository->findBy(['ticket' => $t]);
        if($wingros){
            return $this->render('admin/groslot.html.twig', [
                'user' =>$wingros['0'],
                'date' =>$date, ]);}
                else{

                    return $this->render('admin/groslot.html.twig', [
                        'user' =>  null,
                        'date' =>$date, ]);}
                }
      
     
     

        /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/dowloadMails", name="Mails")
     */
    public function dowloadletter()
    {
              
                // Fetch all users from the UserRepository
            $users = $this->userRepository->findAll();
            
            // Render the HTML view with the users data
            $html = $this->renderView('admin/list.html.twig', [
                'users' => $users,
            ]);
        
            // Create a Dompdf instance and set options
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);

            $dompdf = new Dompdf($options);

            // Set paper size to A4 and orientation to portrait
            $dompdf->setPaper('A4', 'portrait');

            // Load HTML content
            $dompdf->loadHtml($html);

            // Render the PDF
            $dompdf->render();
            
            // Stream the generated PDF as a response
            return new Response(
                $dompdf->stream('resume.pdf', ["Attachment" => false]),
                Response::HTTP_OK,
                ['Content-Type' => 'application/pdf']
            );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/userDownload", name="userMails")
     */
    public function dowloadluser()
    {
              
        $users = $this->userRepository->findAll();
        
       
        $html = $this->renderView('admin/userdownload.html.twig', [
            'users' =>$users, ]);
     
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
             
            return new Response (
                $dompdf->stream('resume', ["Attachment" => false]),
                Response::HTTP_OK,
                ['Content-Type' => 'application/pdf']
            );
        
    }


    // role employee


   
            
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/employe", name="app_employe")
     */
    public function employe(Request $request): Response
    {   $usersWithGains = $this->userRepository->findAllUsersWithGains(); // Implémentez cette méthode dans UserRepository

        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login', []);  
        }
        
        
        return $this->render('employe/index.html.twig', [
            'users_with_gains' => $usersWithGains,
        ]);
    }
    

        /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/gains", name="gains")
     */
    public function gains(Request $request,$id): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login', []);  
        }
        
        
        return $this->render('employe/gains.html.twig');
    }


    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/employe/codeSend", name="app_code_send")
     */
    public function codeSend(Request $request): Response
    {
        $ticketsToUpdate = $request->request->get('gain_id');
        $ticket = $this->winnersRepository->find($ticketsToUpdate);
        $ticket->setSend(true);
        $user=$ticket->getUser();
        $wins= $this->winnersRepository->findBy(['user' => $user]);;
        $this->em->persist($ticket);
        $this->em->flush();
        $surname = $user->getSurname(); // Or however you can get the username from the User object
        $this->addFlash('Success', "Vous avez donné le gain à *" . $surname . "* ! "); 
        return $this->render('employe/gains.html.twig', [
            'wins' => $wins,
        ]);
       
    }


        /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/employe/checkuser/checkcode", name="app_check_customer")
     */
    public function employeCheckuser(Request $request): Response
    {
        $userEmail = $request->request->get('mail');
        $user = $this->userRepository->findBy(['email' => $userEmail]);
        $wins = $this->winnersRepository->findBy(['user' => $user]);
        
        if($user == null){
            $this->addFlash('Erreur',"Cet utilisateur n'existe pas ! ");  
            return $this->redirectToRoute('app_employe');

        }


        if($wins == null){
            $this->addFlash('Erreur',"Cet utilisateur n'a pas de gain ! ");  
            return $this->redirectToRoute('app_employe');

        }

      
     
       
        return $this->render('employe/gains.html.twig', [
            'wins' => $wins,
        ]);

    }
    

    
}