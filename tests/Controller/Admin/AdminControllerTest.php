<?php

namespace App\Tests\Controller\Admin;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends WebTestCase
{
    public function testAdminAccess()
    {
        $client = self::createClient();
        $client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function testAdminIndex()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('admin@admin.com');

        $client->loginUser($user);

        $client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
    }
}