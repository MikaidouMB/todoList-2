<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppTestFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        self::bootKernel();

        $this->client->followRedirects();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);

    }

    public function loginUser(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $this->client->submit($form, ['_username' => 'testUser', '_password' => '00000']);
    }

    public function testSuccessfulLogin()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            "_username" =>'testUser',
            '_password' => '00000',
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoginWithBadCredentials()
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            "_username" =>'badUsername',
            '_password' => 'badPassword',
            ]);

        $this->client->submit($form);
        $this->assertSelectorTextContains('.error-login','Invalid credentials.');
    }

    public function testRegisterWithWrongEmail(): void
    {
        $this->loginUser();

        $crawler = $this->client->request('GET', '/admin/create');

        $form = $crawler->selectButton('Ajouter')->form(
            [
                'registration_form[username]' =>'testUser',
                'registration_form[email]' => 'userTest1@hotmail.fr',
                'registration_form[password][first]' => 'badPassword',
                'registration_form[password][second]' =>'Bad password2',
            ]
        );

        $this->client->submit($form);
        $this->assertSelectorExists('div:contains("There is already an account with this email")');
    }

    public function testRegisterWithWrongPasswords(): void
    {
        $this->loginUser();
        $crawler = $this->client->request('GET', '/admin/create');

        $form = $crawler->selectButton('Ajouter')->form([
                'registration_form[username]' =>'testUser',
                'registration_form[email]' => 'userTest156@hotmail.fr',
                'registration_form[password][first]' => 'badPassword',
                'registration_form[password][second]' =>'Bad password2',
            ]
        );
        $this->client->submit($form);

        $this->assertSelectorExists('div:contains("Les deux mots de passe doivent correspondre.")');
    }

    public function testRegisterSuccessfully(): void
    {
        $this->loginUser();

        $crawler = $this->client->request('GET', '/admin/create');

        $form = $crawler->selectButton('Ajouter')->form([
                'registration_form[username]' =>'testUser',
                'registration_form[email]' => 'userTest12@hotmail.fr',
                'registration_form[password][first]' => '00000',
                'registration_form[password][second]' =>'00000',
            ]
        );
        $this->client->submit($form);

        $this->assertEquals('/admin/users',$this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }
    public function testEditUserSuccessfully(): void
    {
        $this->loginUser();

        $crawler = $this->client->request('GET', '/admin/users/1/edit');

        $form = $crawler->selectButton('Modifier')->form([
                'user[username]' =>'testUser',
                'user[password][first]' => '00000',
                'user[password][second]' =>'00000',
                'user[email]' => 'userTest12@hotmail.fr',
            ]
        );
        $this->client->submit($form);

        $this->assertEquals('/admin/users',$this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }
    public function testRemoveUserSuccessfully()
    {
        $this->loginUser();
        $crawler = $this->client->request('GET', '/admin/users/3/delete');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }
}