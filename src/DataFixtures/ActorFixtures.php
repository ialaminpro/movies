<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ActorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $actor = new Actor();
        $actor->setName("Charles Roven");
        $manager->persist($actor);

        $actor1 = new Actor();
        $actor1->setName("David Thion");
        $manager->persist($actor1);

        $actor2 = new Actor();
        $actor2->setName("Daniel Lupi");
        $manager->persist($actor2);

        $actor3 = new Actor();
        $actor3->setName("Pamela Koffler");
        $manager->persist($actor3);

        $actor4 = new Actor();
        $actor4->setName("Mark Johnson");
        $manager->persist($actor4);

        $manager->flush();
    }
}
