<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $slugFactory = Slugify::create();
        for ($i=0;$i<30;$i++) {
            $ad = new Ad();
            $content = '<p>' . join('<p></p>', $faker->paragraphs(5)) . '</p>';
            $title = $faker->sentence(2);
            $ad->setTitle($title)
                ->setSlug($slugFactory->slugify($title))
                ->setCoverImage($faker->imageUrl(1000, 300))
                ->setIntroduction($faker->paragraph(2))
                ->setContent($content)
                ->setRooms(mt_rand(1,5))
                ->setPrice(mt_rand(25, 99));

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
