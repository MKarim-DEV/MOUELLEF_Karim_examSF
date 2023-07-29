<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{ private $hasher;
    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher=$hasher;
    }
    public function load(ObjectManager $manager): void
    {
        // for($i = 0; $i < 20; $i++){

        //     $user = new User();
        //     $user->setEmail("email" . $i. "@gmail.com");
        //     $encodedPassword=$this->hasher->hashPassword($user, "user");
        //     $user->setPassword($encodedPassword);
        //     $user->setRoles(["ROLE_USER"]);
        //     $manager->persist($user);
        // }

        $Admin= new User();
        $Admin->setEmail("rh@humanbooster.com)");
        $encodedPassword=$this->hasher->hashPassword($Admin, "rh123@");
        $Admin->setPassword($encodedPassword);
        $Admin->setRoles(["ROLE_RH"]);
        $Admin->setName("");
        $Admin->setFirstname("");
        $Admin->setPicture("");
        $Admin->setDivision("");
        $Admin->setContract("");

        $manager->persist($Admin);
        $manager->flush();

    }
}

