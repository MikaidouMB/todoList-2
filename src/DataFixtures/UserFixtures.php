<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        // Admin
        $user = new User;

        $plainPassword = '000000';

        $newPassword = $this->hasher->hashPassword($user, $plainPassword);

        $user->setUsername('admin');
        $user->setEmail('admin@admin.fr');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($newPassword);

        $this->addReference('user-1', $user);

        $manager->persist($user);

        // User
        $user = new User;

        $plainPassword = '000000';

        $newPassword = $this->hasher->hashPassword($user, $plainPassword);

        $user->setEmail('user@user.fr');
        $user->setUsername('user');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($newPassword);

        $this->addReference('user-2', $user);

        $manager->persist($user);

        // Anonymous user
        $user = new User;

        $plainPassword = '000000';

        $newPassword = $this->hasher->hashPassword($user, $plainPassword);

        $user->setEmail('anonymous@anonymous.fr');
        $user->setUsername('anonymous');
        $user->setRoles(['ROLE_ANONYMOUS']);
        $user->setPassword($newPassword);

        $this->addReference('user-3', $user);

        $manager->persist($user);
            $manager->flush();
        }

}
