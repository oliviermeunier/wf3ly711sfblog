<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Factory\PostFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
//        $faker = \Faker\Factory::create('fr_FR');
//
//        $post = new Post();
//        $post->setTitle($faker->sentence());
//        $post->setContent($faker->text(2000));
//        $post->setAuthor($faker->name());
//        $post->setImage('https://picsum.photos/seed/post/750/300');
//        $post->setCreatedAt($faker->dateTimeBetween('-3 years', 'now', 'Europe/Paris'));
//
//        $manager->persist($post);

        // Création de 10 articles de test
        PostFactory::new()->createMany(10);

        $manager->flush();
    }
}
