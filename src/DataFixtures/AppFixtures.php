<?php

namespace App\DataFixtures;

use App\Entity\Evenement;
use App\Entity\Lieu;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ){
    }


    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        // CREER LES UTILISATEURS
        // LE MOT DE PASSE DE L'UTILISATEUR EST "LemonInteractive{$i}*"

        $utilisateurs = [];

        for($i=0; $i<3; $i++){
            $utilisateur = new Utilisateur();
            $utilisateur->setEmail($faker->email())
                        ->setPrenom($faker->firstName())
                        ->setNom($faker->lastName())
                        ->setPassword($this->hasher->hashPassword($utilisateur, "LemonInteractive{$i}*"));
            if($i == 0){
                $utilisateur->setRoles(['ROLE_ADMIN']);
            }
            $manager->persist($utilisateur);
            $utilisateurs[] = $utilisateur;
        }

        $lieux = [];

        for($i=0; $i<10; $i++){
            $lieu = new Lieu();
            $lieu->setLabel($faker->company());
            $manager->persist($lieu);
            $lieux[] = $lieu;
        }

        $evenements = [];

        for($i=0; $i<10; $i++){
            $evenement = new Evenement();
            $dt_debut = $faker->dateTimeBetween('now', '+1 month');
            $dt_fin = date_add(clone $dt_debut, date_interval_create_from_date_string('1 hour'));
            $evenement->setTitre($faker->sentence(3))
                      ->setDescription($faker->sentence(10))
                      ->setDebut($dt_debut)
                      ->setFin($dt_fin)
                      ->setLieu($lieux[$faker->numberBetween(0, count($lieux) - 1)])
                      ->setCreateur($utilisateurs[$faker->numberBetween(0, count($utilisateurs) - 1)]);
            $evenements[] = $evenement;
            $manager->persist($evenement);
        }

        for($i=0; $i<5; $i++){
            $evenement = $evenements[$faker->numberBetween(0, count($evenements) - 1)];
            $evenement->addInscrit($utilisateurs[$faker->numberBetween(0, count($utilisateurs) - 1)]);
            $manager->persist($evenement);
        }

        $manager->flush();
    }
}
