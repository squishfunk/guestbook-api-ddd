<?php

namespace App\Tests\DataFixtures;

use App\User\Domain\Entity\User;
use App\User\Domain\ValueObject\Email;
use App\User\Domain\ValueObject\Password;
use App\User\Infrastructure\Persistence\DoctrineUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User(
            'Damian',
            new Email('test@gmail.com'),
            new Password('Testest123')
        );

        $userDoctrine = DoctrineUser::fromDomain($user);

        $manager->persist($userDoctrine);
        $manager->flush();
    }
}
