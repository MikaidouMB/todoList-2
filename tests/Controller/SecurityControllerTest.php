<?php

namespace App\Tests\Controller;

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

    public function testLogin()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            "_username" =>'testUser',
            '_password' => '00000'
        ]);

        $this->client->submit($form);

        $this->assertSelectorTextContains('.error-login','Invalid credentials.');
    }

    public function testRegisterWithBadCredentials(): void
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
}