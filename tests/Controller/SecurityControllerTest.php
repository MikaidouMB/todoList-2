<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
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
        $this->databaseTool->loadFixtures([UserFixtures::class]);


    }

    //Test s'il y a un formulaire dans la page login
    public function testLoginpage(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('form');
        $this->assertSelectorNotExists('.div','Invalid credentials.');

    }

    public function testSuccessfulLogin()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            "_username" =>'testUser',
            '_password' => '00000'
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
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


    public function testRegisterSuccessfully(): void
    {
        $crawler = $this->client->request('GET', '/register');
        $userRepository = static::getContainer()->get(UserRepository::class);
        $form = $crawler->selectButton('Ajouter')->form([
                "registration_form[username]" =>'testUser',
                'registration_form[email]' => 'userTest156@hotmail.fr',
                'registration_form[password][first]' => '0000',
                'registration_form[password][second]' =>'0000'
            ]
        );
        $this->client->submit($form);
        $this->assertEquals('/',$this->client->getRequest()->getRequestUri());
        $testUser = $userRepository->findOneByEmail('userTest156@hotmail.fr');
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/');
    }

}