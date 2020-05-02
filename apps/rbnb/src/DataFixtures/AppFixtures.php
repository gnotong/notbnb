<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\AdLike;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Role;
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

    public function load(ObjectManager $manager): void 
    {
        $faker = Factory::create();
        $users = [];
        $genders = ['male', 'female'];

        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Gabriel')
            ->setLastName('Notong')
            ->setEmail('gabs@gmail.com')
            ->setPicture('https://randomuser.me/api/portraits/men/53.jpg')
            ->setIntroduction($faker->sentence)
            ->setDescription('<p>' . join('<p></p>', $faker->paragraphs(2)) . '</p>')
            ->setHash($this->encoder->encodePassword($adminUser, 'password'))
            ->addUserRole($adminRole);
        $manager->persist($adminUser);

        // Manage fake users
        for ($i = 1; $i <= 10; $i++) {
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
        for ($i = 0; $i < 30; $i++) {
            $ad = new Ad();
            $content = '<p>' . join('<p></p>', $faker->paragraphs(5)) . '</p>';
            $title = $faker->sentence(2);

            $user = $users[mt_rand(0, count($users) - 1)];

            $picture = 'https://i.picsum.photos/id/';
            $pictureId = $faker->numberBetween(1, 500) . '/600/300.jpg';
            $imageApiUrl = $picture . $pictureId;

            $ad->setTitle($title)
                ->setCoverImage($imageApiUrl)
                ->setIntroduction($faker->paragraph(2))
                ->setContent($content)
                ->setRooms(mt_rand(1, 5))
                ->setPrice(mt_rand(25, 99))
                ->setAuthor($user);

            // manage Ad's Images
            for ($k = 0; $k <= mt_rand(2, 5); $k++) {
                $image = new Image();

                $pictureId = $faker->numberBetween(1, 500) . '/600/300.jpg';
                $imageApiUrl = $picture . $pictureId;

                $image->setUrl($imageApiUrl)
                    ->setCaption($faker->sentence(2))
                    ->setAd($ad);

                $manager->persist($image);
            }

            // manage Ad's Booking
            for ($j = 1; $j <= mt_rand(0, 10); $j++) {
                $booking = new Booking();

                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');

                // number of nights
                $duration = mt_rand(1, 5);

                // clone is used here in order to not modify startDate. So, a copy is used instead
                $endDate = (clone $startDate)->modify("+{$duration} days");

                $booker = $users[mt_rand(0, count($users) - 1)];

                $amount = number_format(($ad->getPrice() ? $ad->getPrice() : 0) * $duration, 2);

                $comment = $faker->paragraph();

                $booking->setBooker($booker)
                    ->setAd($ad)
                    ->setCreatedAt($createdAt)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setAmount((float)$amount)
                    ->setComment($comment);

                $manager->persist($booking);

                // manage comments, some ads may not have a comment
                if (mt_rand(0, 1)) {
                    $comment = new Comment();
                    $comment->setAuthor($booker)
                        ->setRating(mt_rand(0, 5))
                        ->setAd($ad)
                        ->setContent('<p>' . join('<p></p>', $faker->paragraphs(mt_rand(1, 3))) . '</p>');

                    $manager->persist($comment);
                }

                // manage likes
                if (mt_rand(0,1)) {
                    $like = new AdLike();
                    $like->setUser($booker)
                        ->setAd($ad);

                    $manager->persist($like);
                }
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
