<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $author = $manager->getRepository(User::class)->findAll()[1];
        $faker = Factory::create();

        for ($i=0; $i < 5; $i++) {
            $post = new Post();
            $post->setTitle($faker->sentence(rand(2, 8)));
            $post->setContent($faker->realText($faker->numberBetween(50, 250)));
            $post->setAuthor($author);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
