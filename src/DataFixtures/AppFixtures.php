<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Phone;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // creating some phones
        $faker = Faker\Factory::create('fr_FR');

        $phoneVersion = ["OneMinus", "xPhone", "Cerberus", "Noukia", "chiaomi", "Sonix"];

        for ($i = 1; $i < 25; $i++) {
            $version = $faker->randomElement($phoneVersion);
            $phone = new Phone();
            $phone->setName('Bilemo ' . $version . '-' . mt_rand(10, 80));
            $phone->setDescription($faker->sentence);
            $phone->setPrice($faker->numberBetween(350, 1280));

            $manager->persist($phone);
        }

        //creating somes clients

        $client = new Client();
        $client->setName('Orange');
        $this->addReference('Orange', $client);
        $client->setEmail('orange@orange.fr');
        $client->setPassword($this->encoder->encodePassword($client, 'password'));
        $manager->persist($client);

        $client = new Client();
        $client->setName('Free');
        $this->addReference('Free', $client);
        $client->setEmail('free@free.fr');
        $client->setPassword($this->encoder->encodePassword($client, 'password'));
        $manager->persist($client);

        $client = new Client();
        $client->setName('SFR');
        $this->addReference('SFR', $client);
        $client->setEmail('sfr@sfr.fr');
        $client->setPassword($this->encoder->encodePassword($client, 'password'));
        $manager->persist($client);

        $client = new Client();
        $client->setName('Bouygue');
        $this->addReference('Bouygue', $client);
        $client->setEmail('bouygue@bouygue.fr');
        $client->setPassword($this->encoder->encodePassword($client, 'password'));
        $manager->persist($client);

        for($j=1 ; $j < 20; $j++)
        {
            $user = new User();
            $user->setName($faker->name);
            $user->setEmail($faker->email);
            $user->setPhone($faker->phoneNumber);
            $user->setClient($this->getReference('Orange'));
            $manager->persist($user);
        }

        for($k=1 ; $k < 25; $k++)
        {
            $user = new User();
            $user->setName($faker->name);
            $user->setEmail($faker->email);
            $user->setPhone($faker->phoneNumber);
            $user->setClient($this->getReference('Free'));
            $manager->persist($user);
        }

        for($f=1 ; $f < 14; $f++)
        {
            $user = new User();
            $user->setName($faker->name);
            $user->setEmail($faker->email);
            $user->setPhone($faker->phoneNumber);
            $user->setClient($this->getReference('SFR'));
            $manager->persist($user);
        }

        for($m=1 ; $m < 14; $m++)
        {
            $user = new User();
            $user->setName($faker->name);
            $user->setEmail($faker->email);
            $user->setPhone($faker->phoneNumber);
            $user->setClient($this->getReference('Bouygue'));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
