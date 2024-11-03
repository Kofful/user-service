<?php

namespace App\Repository;

use App\Dto\Auth\LoginDto;
use App\Dto\User\GetUsersDto;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function checkLoginCredentials(LoginDto $loginDto): ?User
    {
        $user = new User();
        $user->setLogin($loginDto->login);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $loginDto->pass);
        $user->setPassword($hashedPassword);

        return $this->findOneBy([
            'login' => $user->getLogin(),
            'password' => $user->getPassword(),
        ]);
    }

    public function getUsers(?GetUsersDto $getUsersDto): array
    {
        $criteria = ['login' => $getUsersDto?->login];
        // remove empty fields
        $criteria = array_filter($criteria, fn($value) => !is_null($value));

        return $this->findBy($criteria);
    }
}
