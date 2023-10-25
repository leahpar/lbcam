<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Tag;
use App\Entity\Truc;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Fakecar;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{
    public function __construct(
        #[Autowire('%uploadDir%')]
        private readonly string $uploadDir,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $slugger = new AsciiSlugger();

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Fakecar($faker));
        $faker->addProvider(new FakerPicsumImagesProvider($faker));

        $tags = [];

        $props = [
            ...$faker->vehicleProperties,
            $faker->vehicleType,
            $faker->vehicleType,
            $faker->vehicleType,
            $faker->vehicleType,
            $faker->vehicleType,
            $faker->vehicleType,
            $faker->vehicleType,
        ];

        foreach (array_unique($props) as $prop) {
            $tag = new Tag();
            $tag->nom = $prop;
            $tag->slug = strtolower($slugger->slug($tag->nom));
            $manager->persist($tag);
            $tags[] = $tag;
        }

        for ($i = 0; $i < 40; $i++) {
            $truc = new Truc();
            $truc->nom = $faker->vehicle;
            $truc->slug = strtolower($slugger->slug($truc->nom));
            $truc->description = $faker->paragraphs(random_int(2, 3), true);
            for ($j = 0; $j < random_int(1, 5); $j++) {
                $truc->addTag($tags[random_int(0, count($tags) - 1)]);
            }
            $manager->persist($truc);

            for ($j = 0; $j < random_int(1, 5); $j++) {
                $file = $faker->image(dir: $this->uploadDir, width: 640, height: 480, fullPath: false);
                $image = new Image();
                $image->filename = $file;
                $truc->addImage($image);
                $manager->persist($image);
            }
        }

        $manager->flush();
    }
}
