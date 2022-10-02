<?php

namespace App\Tests\Repository;

use App\DataFixtures\AppFixtures;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    /** @var AbstractDatabaseTool */
    protected AbstractDatabaseTool $databaseTool;

    public function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testCount() {
        $this->databaseTool->loadFixtures([AppFixtures::class]);
        $users = self::getContainer()->get(UserRepository::class)->count([]);
        $this->assertEquals(10,$users);
    }
}