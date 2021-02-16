<?php


namespace App\Tests\Repository;

use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserRepositoryTest extends KernelTestCase
{
    use ResetDatabase;

    public function testCount()
    {
        self::bootKernel();

        UserFactory::new()->createMany(10);
        $users = self::$container->get(UserRepository::class)->count([]);

        $this->assertEquals(10, $users);
    }
}