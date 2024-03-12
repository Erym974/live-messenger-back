<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'swiftchat:database-init',
    description: 'Reset the database',
    aliases: ['sw:db:init'],
)]
class SwiftchatDatabaseInitCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force to be sure')
            ->addOption('fixtures', null, InputOption::VALUE_NONE, 'Add fixtures with')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$input->getOption('force')) {
            $io->error('You must use the --force option to execute this command.');
            return Command::FAILURE;
        }

        $deleteDb = new ArrayInput([
            'command' => 'd:d:d',
            '-f'  => true,
        ]);

        $createDb = new ArrayInput([
            'command' => 'd:d:c'
        ]);

        $migrateDb = new ArrayInput([
            'command' => 'd:m:m',
            '--no-interaction'  => true,
        ]);

        $fixtures = new ArrayInput([
            'command' => 'doctrine:fixtures:load',
            '--no-interaction'  => true,
        ]);

        $outputDeleteDb = $this->getApplication()->doRun($deleteDb, $output);
        if($outputDeleteDb != 0) {
            $io->error('An error occurred while deleting the database');
            return Command::FAILURE;
        }
        $outputCreateDb = $this->getApplication()->doRun($createDb, $output);
        if($outputCreateDb != 0) {
            $io->error('An error occurred while creating the database');
            return Command::FAILURE;
        }
        $outputMigrateDb = $this->getApplication()->doRun($migrateDb, $output);
        if($outputMigrateDb != 0) {
            $io->error('An error occurred while migrating the database');
            return Command::FAILURE;
        }

        if($input->getOption('fixtures')) {
            $outputFixtures = $this->getApplication()->doRun($fixtures, $output);
            if($outputFixtures != 0) {
                $io->error('An error occurred while adding fixtures');
                return Command::FAILURE;
            }
        }

        $io->success('Database reset successfully');

        return Command::SUCCESS;
    }
}
