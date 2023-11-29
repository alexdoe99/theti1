<!-- <?php
// tests/Controller/RegistrationControllerTest.php
// tests/Controller/RegistrationControllerTest.php

// namespace App\Tests\Controller;
// use App\Entity\User;
// use App\Entity\Game;

// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
// use Doctrine\ORM\EntityManagerInterface;

// class RegistrationControllerTest extends WebTestCase
// {
//     public function testSubmitRegistrationForm()
//     {   
//         $client = static::createClient();
//         $entityManager = self::getContainer()->get(EntityManagerInterface::class);

//         // Créez une instance de Game et définissez la closingDate
//         $game = new Game();
//         $game->setClosingDate(new \DateTime());

//         // Créez une instance de User si nécessaire pour votre test
//         $user = new User();
//         $user = new User();
//         $user->setEmail('john.doe@example.com');
//         $user->setPassword('securepassword');
//         $entityManager->persist($user);
//         $entityManager->flush();
//         // Récupère le crawler après avoir visité la page d'inscription
//         $crawler = $client->request('GET', '/register');

//         // Sélectionne le formulaire en utilisant le texte du bouton de soumission, par exemple "S'inscrire"
//         $buttonCrawlerNode = $crawler->selectButton("S'inscrire");

//         // Récupère le formulaire et définis les valeurs des champs
//         $form = $buttonCrawlerNode->form([
//             'registration_form[surname]' => 'Doe',
//             'registration_form[name]' => 'John',
//             'registration_form[address]' => '123 Main Street',
//             'registration_form[city]' => 'Anytown',
//             'registration_form[country]' => 'Countryland',
//             'registration_form[aboutUs]' => 'internet',
//             'registration_form[email]' => 'john.doe@example.com',
//             'registration_form[plainPassword][first]' => 'password123',
//             'registration_form[plainPassword][second]' => 'password123',
//             // Assurez-vous que les noms des champs correspondent exactement à ceux générés dans votre formulaire Symfony
//         ]);

//         // Cochez les cases si nécessaire, exemple pour la newsletter
//         $form['registration_form[agreeTerms]']->tick();
//         $form['registration_form[newsletter]']->tick();

//         // Soumet le formulaire
//         $client->submit($form);

//         // Vérifiez que la réponse est un succès ou que vous êtes redirigé vers une autre page, comme la page de connexion
//         $this->assertResponseRedirects('/register');

//         // Suivez la redirection et vérifiez que la page de connexion s'affiche
//         $crawler = $client->followRedirect();
//         $this->assertResponseIsSuccessful();
//         $this->assertSelectorTextContains('p', '*Pour toute commande effectué avant le 20 décembre');
//     }
// } -->
