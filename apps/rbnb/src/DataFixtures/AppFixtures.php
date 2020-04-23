<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $users = [];
        $genders = ['male', 'female'];

        // Manage fake users
        for($i = 1; $i <= 10; $i++) {
            $user = new User();

            $gender = $faker->randomElement($genders);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($gender == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');
            $user->setFirstName($faker->firstName($gender))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence)
                ->setDescription('<p>' . join('<p></p>', $faker->paragraphs(2)) . '</p>')
                ->setPicture($picture)
                ->setHash($hash);

            $manager->persist($user);
            $users[] = $user;
        }

        // Manage Fake Ads
        for ($i=0;$i<30;$i++) {
            $ad = new Ad();
            $content = '<p>' . join('<p></p>', $faker->paragraphs(5)) . '</p>';
            $title = $faker->sentence(2);

            $user = $users[mt_rand(0, count($users) -1)];

            $ad->setTitle($title)
                ->setCoverImage("http://placehold.it/1000x400")
                ->setIntroduction($faker->paragraph(2))
                ->setContent($content)
                ->setRooms(mt_rand(1,5))
                ->setPrice(mt_rand(25, 99))
                ->setAuthor($user);

            for($k = 0; $k <= mt_rand(2, 5); $k++) {
                $image = new Image();
                $image->setUrl("http://placehold.it/1000x400")
                    ->setCaption($faker->sentence(2))
                    ->setAd($ad);

                $manager->persist($image);
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
