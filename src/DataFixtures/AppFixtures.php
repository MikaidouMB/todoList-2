<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['group1'];
    }

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = new Factory;
        $faker = $faker::create('fr_FR');

        //admin
        $user = new User;

        $plainPassword = '000000';

        $newPassword = $this->hasher->hashPassword($user, $plainPassword);

        $user->setUsername('admin');
        $user->setEmail('admin@admin.fr');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($newPassword);

        $this->addReference('user-1', $user);
        $manager->persist($user);

        $task = new Task;
        $task->setUser($user);
        $task->setContent($faker->paragraph(2));
        $task->setTitle($faker->sentence());
        $manager->persist($task);


        // User
        $user = new User;

        $plainPassword = '000000';

        $newPassword = $this->hasher->hashPassword($user, $plainPassword);

        $user->setEmail('user@user.fr');
        $user->setUsername('user');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($newPassword);


        $manager->persist($user);

        $task = new Task;
        $task->setUser($user);
        $task->setContent($faker->paragraph(2));
        $task->setTitle($faker->sentence());
        $manager->persist($task);


        $user = new User;

        $plainPassword = '000000';

        $newPassword = $this->hasher->hashPassword($user, $plainPassword);

        $user->setEmail('user3@user.fr');
        $user->setUsername('user3');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($newPassword);

        $manager->persist($user);
        $task = new Task;
        $task->setUser($user);
        $task->setContent($faker->paragraph(2));
        $task->setTitle($faker->sentence());
        $manager->persist($task);


        $task = new Task;
        $task->setUser(null);
        $task->setContent($faker->paragraph(2));
        $task->setTitle($faker->sentence());
        $manager->persist($task);

        $manager->flush();
    }

}
