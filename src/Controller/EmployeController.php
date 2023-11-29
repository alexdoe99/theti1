<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\WinnersRepository;
use App\Repository\UserRepository;
use App\Entity\Winners;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class EmployeController extends AbstractController
{

    protected $em;
    protected $winnersRepository;
    protected $userRepository;

    public function __construct(EntityManagerInterface $em,UserRepository $userRepository, WinnersRepository $winnersRepository){
        $this->em = $em;
        $this->winnersRepository = $winnersRepository;
        $this->userRepository = $userRepository;
    }
            
    // /**
    //  * @IsGranted("ROLE_EMPLOYE")
    //  * @Route("/employe", name="app_employe")
    //  */
    // public function employe(Request $request): Response
    // {
    //     $user = $this->getUser();
    //     if(!$user){
    //         return $this->redirectToRoute('app_login', []);  
    //     }
        
        
    //     return $this->render('employe/index.html.twig');
    // }

    /**
     * @IsGranted("ROLE_EMPLOYE")
     * @Route("/employe", name="app_employe")
     */
    public function employe(): Response
    {
        // Assurez-vous que cette méthode retourne bien ce que vous attendez.
        $usersWithGains = $this->userRepository->findAllUsersWithGains();

        // Utilisez cette instruction pour débugger et vérifier le contenu de $usersWithGains
        dump($usersWithGains);

        // La méthode render doit être appelée avec le tableau de variables à passer au template.
        return $this->render('employe/index.html.twig', [
            'users_with_gains' => $usersWithGains,
        ]);
    }
            
        /**
     * @IsGranted("ROLE_EMPLOYE")
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
     * @IsGranted("ROLE_EMPLOYE")
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
     * @IsGranted("ROLE_EMPLOYE")
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