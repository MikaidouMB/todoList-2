<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppTestFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private $client;

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

    public function testCreateTaskSuccessfully(): void
    {
        $this->loginUser();

        $crawler = $this->client->request('GET', '/tasks/create');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'letitre';
        $form['task[content]'] = 'lecontenue';

        $this->client->submit($form);

        $this->assertEquals('/tasks',$this->client->getRequest()->getRequestUri());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testEditTaskSuccessfully()
    {
        $this->loginUser();

        $crawler = $this->client->request('GET', '/tasks/10/edit');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'letitre';
        $form['task[content]'] = 'lecontenue';

        $this->client->submit($form);

        $this->assertEquals('/tasks',$this->client->getRequest()->getRequestUri());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }

    public function testToggleSuccessfully()
    {
        $this->loginUser();
        $crawler = $this->client->request('GET', '/tasks/4/toggle');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }


    public function testRestrictedRemoveTask()
    {
        $crawler = $this->client->request('GET', '/tasks/4/delete');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-danger')->count());
    }

    public function testRemoveTaskSuccessfully()
    {
        $this->loginUser();
        $crawler = $this->client->request('GET', '/tasks/1/delete');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testRemoveTaskSuccessfullyByAdmin()
    {
        $this->loginUser();
        $crawler = $this->client->request('GET', '/tasks/1/delete');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }
}