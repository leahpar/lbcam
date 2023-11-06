<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Tag;
use App\Entity\Truc;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Fakecar;
use Faker\Provider\Person;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function __construct(
        #[Autowire('%upload_dir%')]
        private readonly string $uploadDir,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $slugger = new AsciiSlugger();

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Fakecar($faker));
        $faker->addProvider(new FakerPicsumImagesProvider($faker));

        $tags = [];

        $props = [
            'Towbar', 'Aircondition', 'GPS', 'Leather seats', 'Roof Rack',
            'hatchback', 'sedan', 'small', 'convertible', 'SUV', 'MPV', 'coupe', 'station wagon',
            'gas', 'electric', 'diesel', 'hybrid',
        ];

        $users = [];
        $admin = new User();
        $admin->nom = 'Raphaël';
        $admin->setUsername("raphael@lbcam.fr");
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'azeaze'));
        $manager->persist($admin);
        $users[] = $admin;

        for ($i = 0; $i < 3; $i++) {
            $user = new User();
            $user->nom = $faker->name();
            $user->setUsername(strtolower($slugger->slug($user->nom)).'@example.com');
            $user->setPassword("azeazeaze");
            $manager->persist($user);
            $users[] = $user;
        }

        foreach (array_unique($props) as $prop) {
            $tag = new Tag();
            $tag->nom = $prop;
            $tag->slug = strtolower($slugger->slug($tag->nom));
            $manager->persist($tag);
            $tags[] = $tag;
        }

        for ($i = 0; $i < 25; $i++) {
            $truc = new Truc();
            $truc->nom = $faker->vehicle();
            $truc->slug = strtolower($slugger->slug($truc->nom));
            $truc->description = $faker->paragraphs(random_int(2, 3), true);
            $truc->user = $users[random_int(0, count($users) - 1)];
            for ($j = 0; $j < random_int(1, 5); $j++) {
                $truc->addTag($tags[random_int(0, count($tags) - 1)]);
            }
            $manager->persist($truc);

            for ($j = 0; $j < random_int(1, 5); $j++) {
//                $file = $faker->image(dir: $this->uploadDir, width: 640, height: 480, fullPath: false);
                $file = $faker->image($this->uploadDir, 640,480, false);
                $image = new Image();
                $image->filename = $file;
                $truc->addImage($image);
                $manager->persist($image);
            }
        }

        $manager->flush();
    }
}
