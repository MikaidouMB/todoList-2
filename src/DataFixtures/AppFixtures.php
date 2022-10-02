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
        $array = array(
            "ROLE_USER" => 1,
            "ROLE_ADMIN" => 2,
        );

        for ($i = 0; $i < 10; $i++){
            $user = new User();
            $task = (new Task());
            $task->setUser($user);
            $task->setContent("Content n°$i");
            $task->setTitle("title n°$i");

            $user->setUsername("testUser");
            $user->setEmail("userTest$i@hotmail.fr");
            $user->setPassword("00000");
            $user->setRoles((array)array_rand($array));
            $manager->persist($user);

            $manager->persist($task);

        }
        $manager->flush();
    }
}
