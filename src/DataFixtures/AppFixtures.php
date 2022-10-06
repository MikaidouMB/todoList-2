<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++){
            $user = new User();
            $task = (new Task());
            $task->setUser($user);
            $task->setContent("Content n°$i");
            $task->setTitle("title n°$i");

            $user->setUsername("testUser");
            $user->setEmail("userTest$i@hotmail.fr");
            $user->setPassword("00000");
            $user->setRoles(["ROLE_ADMIN"]);
            $manager->persist($user);

            $manager->persist($task);

        }
        $manager->flush();
    }
}
