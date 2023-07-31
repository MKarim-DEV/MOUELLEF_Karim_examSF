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
        $divisions = ["RH", "Comptabilité", "Informatique", "Direction"];
        $contracts = ["CDI", "CDD", "INTERIM"];
        for($i = 0; $i < 20; $i++){

            $user = new User();
            $user->setEmail("user" . $i. "@gmail.com");
            $encodedPassword=$this->hasher->hashPassword($user, "password".$i);
            $user->setPassword($encodedPassword);
            $user->setRoles(["ROLE_USER"]);
                $user->setName("name".$i);
                $user->setFirstname("firstname".$i);
                $user->setPicture("img/profil.jpg"); // Utilisez le bon chemin vers le dossier public/img
            $user->setDivision($divisions[array_rand($divisions)]);
            $contract = $contracts[array_rand($contracts)];
            $user->setContract($contract);
            if ($contract === "CDD" || $contract === "INTERIM") {
                $user->setEndDate($this->getRandomDate("2022-01-01", "2024-12-31"));
            }
            $manager->persist($user);
        }
        

        $rh= new User();
        $rh->setEmail("rh@humanbooster.com");
        $encodedPassword=$this->hasher->hashPassword($rh, "rh123@");
        $rh->setPassword($encodedPassword);
        $rh->setRoles(["ROLE_RH"]);
        $rh->setName("");
        $rh->setFirstname("");
        $rh->setPicture("");
        $rh->setDivision("");
        $rh->setContract("");

        $manager->persist($rh);
        $manager->flush();

    }
    private function getRandomDate(string $startDate, string $endDate): \DateTimeInterface
    {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $randomTimestamp = mt_rand($startDate, $endDate);

        $randomDate = new \DateTime();
        $randomDate->setTimestamp($randomTimestamp);

        return $randomDate;
    }
}


