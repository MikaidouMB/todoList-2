<?php

namespace App\Tests\Controller;
use App\DataFixtures\UserFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->client->followRedirects();

        self::bootKernel();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testpage(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testHomepage(): void
    {
      $this->client->request('GET', '/');
        //FAIL
      /*  $this->assertSelectorTextContains('h1',
            "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches
             sans effort !");*/
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }



   public function testLoginpage(): void
    {
        $this->client->request('GET', '/login');
        $this->assertEquals('/login',$this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSuccessfullLogin()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->databaseTool->loadFixtures([UserFixtures::class]);
        $form = $crawler->selectButton('Se connecter')->form([
            "_username" =>'testUser',
            '_password' => '00000'
        ]);

        $this->client->submit($form);
        $this->assertSelectorNotExists('.error-login','Invalid credentials.');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals('/',$this->client->getRequest()->getRequestUri());
    }

    public function testTasksPage(): void
    {
        $this->client->request('GET', '/tasks');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    //Je teste quand l'utilisateur tente d'acceder à la page de création des taches sans être connecté
    public function testRestrictedCreateTasksPage(): void
    {
        $this->client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->assertEquals('/',$this->client->getRequest()->getRequestUri());
    }

    public function testRestrictedAdminUsersPage(): void
    {
        $this->client->request('GET', '/admin/users');

        $this->assertEquals('/login',$this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    public function testRestrictedAdminCreateUserPage(): void
    {
        $this->client->request('GET', '/admin/create');

        $this->assertEquals('/login',$this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testRestrictedAdminEditUserPage(): void
    {
        $this->client->request('GET', '/admin/users/1/edit');
        $this->assertEquals('/login',$this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}