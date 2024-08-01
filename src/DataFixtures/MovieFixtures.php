<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setTitle("The Shawshank Redemption");
        $movie->setDescription("A Maine banker convicted of the murder of his wife and her lover in 1947 gradually forms a friendship over a quarter century with a hardened convict, while maintaining his innocence and trying to remain hopeful through simple compassion.");
        $movie->setReleaseYear(1994);
        $movie->setImagePath("https://m.media-amazon.com/images/M/MV5BNDE3ODcxYzMtY2YzZC00NmNlLWJiNDMtZDViZWM2MzIxZDYwXkEyXkFqcGdeQXVyNjAwNDUxODI@._V1_FMjpg_UX1200_.jpg");

        //Add Data To Pivot Table
        $movie->addActor($this->getReference('actor_1'));
        $movie->addActor($this->getReference('actor_2'));

        $manager->persist($movie);

        $movie1 = new Movie();
        $movie1->setTitle("The Godfather");
        $movie1->setDescription("Don Vito Corleone, head of a mafia family, decides to hand over his empire to his youngest son, Michael. However, his decision unintentionally puts the lives of his loved ones in grave danger.");
        $movie1->setReleaseYear(1972);
        $movie1->setImagePath("https://m.media-amazon.com/images/M/MV5BM2MyNjYxNmUtYTAwNi00MTYxLWJmNWYtYzZlODY3ZTk3OTFlXkEyXkFqcGdeQXVyNzkwMjQ5NzM@._V1_QL75_UY562_CR8,0,380,562_.jpg");

        //Add Data To Pivot Table
        $movie1->addActor($this->getReference('actor_2'));
        $movie1->addActor($this->getReference('actor_3'));

        $manager->persist($movie1);

        $movie2 = new Movie();
        $movie2->setTitle("12 Angry Men");
        $movie2->setDescription("The jury in a New York City murder trial is frustrated by a single member whose skeptical caution forces them to more carefully consider the evidence before jumping to a hasty verdict.");
        $movie2->setReleaseYear(1957);
        $movie2->setImagePath("https://m.media-amazon.com/images/M/MV5BMWU4N2FjNzYtNTVkNC00NzQ0LTg0MjAtYTJlMjFhNGUxZDFmXkEyXkFqcGdeQXVyNjc1NTYyMjg@._V1_FMjpg_UX974_.jpg");

        //Add Data To Pivot Table
        $movie2->addActor($this->getReference('actor_4'));
        $movie2->addActor($this->getReference('actor_5'));

        $manager->persist($movie2);

        $manager->flush();
    }
}
