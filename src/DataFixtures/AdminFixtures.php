<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $adminEmail = 'admin@example.com';
        $existingAdmin = $manager->getRepository(Admin::class)->findOneBy(['email' => $adminEmail]);
        if (!$existingAdmin) {
            $admin = new Admin();
            $admin->setEmail($adminEmail);
            $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
            $admin->setRoles(['ROLE_ADMIN']);
            $manager->persist($admin);
        }

        $manager->flush();
    }
}