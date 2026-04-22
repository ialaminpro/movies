<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = (new User())
            ->setEmail('admin@example.com')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'ChangeMe1234!'));

        $viewer = (new User())
            ->setEmail('viewer@example.com');
        $viewer->setPassword($this->passwordHasher->hashPassword($viewer, 'ChangeMe1234!'));

        $manager->persist($admin);
        $manager->persist($viewer);
        $manager->flush();
    }
}
