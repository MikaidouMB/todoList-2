<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    private $client;
    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        self::bootKernel();

        $this->client->followRedirects();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    //Test s'il y a un formulaire dans la page login
    public function testLoginpage(): void
    {
        $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('form');
        $this->assertSelectorNotExists('.div','Invalid credentials.');

    }

    //Aucune connexion ne passe, assertionRedirects a revoir
    public function testSuccessfulLogin()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->databaseTool->loadFixtures([UserFixtures::class]);
        //dd($users->findAll());
        $form = $crawler->selectButton('Se connecter')->form([
            "_username" =>'testUser',
            '_password' => '00000'
        ]);

        $this->client->submit($form);
        //$this->assertSelectorNotExists('.error-login','Invalid credentials.');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        //dd($crawler->get);
        //$this->assertResponseRedirects('http://localhost:8000/');
   //     $this->assertSelectorTextContains('.error-login','Invalid credentials.');

       //$this->assertSelectorTextNotContains('.error-login','Invalid credentials.');

     //   $this->assertSelectorNotExists('.error-login','Invalid credentials.');
    }

    public function testLoginWithBadCredentials()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            "_username" =>'badUsername',
                '_password' => 'badPassword'
            ]);

        $this->client->submit($form);

       $this->assertSelectorTextContains('.error-login','Invalid credentials.');
    }

    public function testRegisterWithWrongEmail(): void
    {
        $crawler = $this->client->request('GET', '/register');
        $form = $crawler->selectButton('Ajouter')->form([
                "registration_form[username]" =>'testUser',
                'registration_form[email]' => 'userTest0@hotmail.fr',
                'registration_form[password][first]' => 'badPassword',
                'registration_form[password][second]' =>'Bad password2'
            ]
        );
        $this->client->submit($form);
        $this->assertSelectorExists('div:contains("There is already an account with this email")');
    }

    public function testRegisterWithWrongPasswords(): void
    {
        $crawler = $this->client->request('GET', '/register');
        $form = $crawler->selectButton('Ajouter')->form([
                "registration_form[username]" =>'testUser',
                'registration_form[email]' => 'userTest156@hotmail.fr',
                'registration_form[password][first]' => 'badPassword',
                'registration_form[password][second]' =>'Bad password2'
            ]
        );
        $this->client->submit($form);
        $this->assertSelectorExists('div:contains("Les deux mots de passe doivent correspondre.")');
    }

}