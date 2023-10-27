<?php

namespace App\Command;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:images:purge',
    description: 'Supprime toutes les images orphelines',
)]
class ImagesPurgeCommand extends Command
{
    public function __construct(
        #[Autowire('%upload_dir%')]
        private readonly string $uploadDir,
        private readonly EntityManagerInterface $em,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $imagesDb = $this->em->getRepository(Image::class)->findAll();
        $fichiersDb = array_map(fn (Image $image) => $image->filename, $imagesDb);

        $fichiers = scandir($this->uploadDir);

        $imagesOrphelines = array_filter(
            $fichiers,
            fn (string $file) => !in_array($file, $fichiersDb) && is_file($this->uploadDir . '/' . $file)
        );

        foreach ($imagesOrphelines as $imageOrpheline) {
            unlink($this->uploadDir . '/' . $imageOrpheline);
        }

        $io->success(count($imagesOrphelines)." images orphelines supprimées");
        return Command::SUCCESS;
    }
}
