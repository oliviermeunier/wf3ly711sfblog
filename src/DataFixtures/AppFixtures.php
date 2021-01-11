<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Factory\CategoryFactory;
use App\Factory\CommentFactory;
use App\Factory\PostFactory;
use App\Factory\UserFactory;
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

        // Création de 10 utilisateurs
        UserFactory::new()->createMany(10);

        // Création d'un compte administrateur
        UserFactory::new()->create([
            'email' => 'admin@admin.com',
            'password' => 'admin',
            'roles' => ['ROLE_ADMIN']
        ]);

        // Création de 5 catégories de test
        CategoryFactory::new()->createMany(5);

        // Création de 10 articles de test
        PostFactory::new()->createMany(10);

        // Création de 50 commentaires
        CommentFactory::new()->createMany(50);

        $manager->flush();
    }
}
