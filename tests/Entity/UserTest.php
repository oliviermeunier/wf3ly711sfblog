<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    private UserPasswordEncoderInterface $userPasswordEncoder;
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        self::bootKernel();
        $this->userPasswordEncoder = self::$container->get('security.user_password_encoder.generic');
        $this->validator = self::$container->get('debug.validator');
    }

    public function getValidUserEntity()
    {
        $user = (new User)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setEmail('alfred.dupont@gmail.com')
                    ->setFirstname('Alfred')
                    ->setLastname('Dupont');

        $this->userPasswordEncoder->encodePassword($user, 'toto');

        return $user;
    }

    public function testValidUser()
    {
        $user = $this->getValidUserEntity();
        $violations = $this->validator->validate($user);
        $this->assertCount(0, $violations);
    }

    public function testInvalidEmailFormat()
    {
        $user = $this->getValidUserEntity()->setEmail('invalidEmail');
        $violations = $this->validator->validate($user);

        $messages = [];

        /** @var ConstraintViolation $error */
        foreach($violations as $violation) {
            $messages[] = $violation->getPropertyPath() . ' => ' . $violation->getMessage();
//            fwrite(STDERR, print_r($violation->getPropertyPath() . ' => ' . $violation->getMessage() . "\n\r", TRUE));
        }

        $this->assertCount(1, $violations, implode(', ', $messages));
    }

    public function testUniqueEmail()
    {
        UserFactory::new()->create([
            'createdAt' => new \DateTimeImmutable(),
            'email' => 'alfred.dupont@gmail.com',
            'firstname' => 'Alfred',
            'lastname' => 'Dupont'
        ]);

        $user = $this->getValidUserEntity();
        $violations = $this->validator->validate($user);

        $messages = [];

        /** @var ConstraintViolation $error */
        foreach($violations as $violation) {
            fwrite(STDERR, print_r($violation->getPropertyPath() . ' => ' . $violation->getMessage() . "\n\r", TRUE));
        }

        $this->assertCount(1, $violations);
    }
}