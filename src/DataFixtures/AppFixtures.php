<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $post= new Post();
            $post->setTitle($faker->word);
            $post->setContent($faker->sentence(6, true));
            $post->setAuthor($faker->firstName);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
