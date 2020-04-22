<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i=0;$i<30;$i++) {
            $ad = new Ad();
            $content = '<p>' . join('<p></p>', $faker->paragraphs(5)) . '</p>';
            $title = $faker->sentence(2);
            $ad->setTitle($title)
                ->setCoverImage($faker->imageUrl(1000, 300))
                ->setIntroduction($faker->paragraph(2))
                ->setContent($content)
                ->setRooms(mt_rand(1,5))
                ->setPrice(mt_rand(25, 99));

            for($k = 0; $k <= mt_rand(2, 5); $k++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl(1000, 300))
                    ->setCaption($faker->sentence(2))
                    ->setAd($ad);

                $manager->persist($image);
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
