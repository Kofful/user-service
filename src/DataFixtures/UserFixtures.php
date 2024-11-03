<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // two admin passwords
        $admin1 = $this->createUser([
            'login' => 'admin',
            'pass' => 'password',
            'phone' => '11111',
            'roles' => ['ROLE_ADMIN'],
        ]);

        $manager->persist($admin1);

        $admin2 = $this->createUser([
            'login' => 'admin',
            'pass' => 'pass',
            'phone' => '22222',
            'roles' => ['ROLE_ADMIN'],
        ]);

        $manager->persist($admin2);

        // two user passwords
        $user1 = $this->createUser([
            'login' => 'test',
            'pass' => 'password',
            'phone' => '33333',
        ]);

        $manager->persist($user1);
        $user2 = $this->createUser([
            'login' => 'test',
            'pass' => 'pass',
            'phone' => '44444',
        ]);

        $manager->persist($user2);

        $manager->flush();
    }

    private function createUser(array $attributes = []) : User
    {
        $user = new User();
        $user->setLogin($attributes['login']);
        $user->setRoles($attributes['roles'] ?? []);
        $user->setPhone($attributes['phone']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $attributes['pass']);
        $user->setPassword($hashedPassword);

        return $user;
    }
}
