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

    public function testHomepage(): void
    {
        $this->client->request('GET', '/');

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

    public function testRestrictedCreateTasksPage(): void
    {
        $this->client->request('GET', '/tasks/create');
        $this->assertResponseHasHeader("http://localhost:8000//");
        $crawler =$this->client->followRedirects();
        dd($crawler);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testRestrictedAdminUsersPage(): void
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