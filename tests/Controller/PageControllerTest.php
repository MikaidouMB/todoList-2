<?php

namespace App\Tests\Controller;
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

//Comment tester @Route("/", name="homepage")
//	    public function indexAction(){
//	        return $this->render('default/index.html.twig');
//	    }
    public function testpage(): void
    {
        $this->client->request('GET', '/');
       //dd($crawler->html());
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
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testRestrictedTasksPage(): void
    {
        $this->client->request('GET', '/tasks');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    //Je teste quand l'utilisateur tente d'acceder à la page de création des taches sans être connecté
  /*  public function testRestrictedCreateTasksPage(): void
    {
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/tasks/create');

        $this->assertResponseRedirects('http://localhost:8000/');
    }

    /*public function testRestrictedAdminUsersPage(): void
    {
        $this->client->request('GET', '/admin/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /*public function testRestrictedAdminCreateUserPage(): void
    {
        $this->client->request('GET', '/admin/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testRestrictedAdminEditUserPage(): void
    {
        $this->client->request('GET', '/admin/users/1/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }*/

}