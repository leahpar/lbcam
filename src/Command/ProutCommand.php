<?php

namespace App\Command;

use Faker\Factory;
use Faker\Provider\Person;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:prout',
    description: 'Add a short description for your command',
)]
class ProutCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $faker = Factory::create('fr_FR');

        for ($i=0; $i < 10; $i++) {
            $io->writeln($faker->unique()->name());
        }

        return Command::SUCCESS;
    }
}
