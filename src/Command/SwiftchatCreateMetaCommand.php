<?php

namespace App\Command;

use App\Entity\Meta;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'swiftchat:create:meta',
    description: 'Create a new Meta settings',
    aliases: ['s:c:m']
)]
class SwiftchatCreateMetaCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'Name of the meta')
            ->addArgument('value', InputArgument::OPTIONAL, 'Default value of the meta')
            ->addArgument('allowed', InputArgument::OPTIONAL, 'Allowed types of value')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name');
        if(!$name) {
            $question = new Question('Insert the name of the new meta : ');
            $name = $helper->ask($input, $output, $question);
        }

        $allowed = $input->getArgument('allowed');
        if(!$allowed) {
            $question = new ChoiceQuestion('Insert the default value of the new meta : ', ['string', 'bool', 'int'], 'string');
            $allowed = $helper->ask($input, $output, $question);
        }

        $value = $input->getArgument('value');
        if(!$value) {

            switch($allowed) {
                case 'string':
                    $question = new Question('Insert the default value of the new meta : ');
                    $value = $helper->ask($input, $output, $question);
                    break;
                case 'bool':
                    $question = new ChoiceQuestion('Insert the default value of the new meta : ', ['true', 'false'], 'true');
                    $value = $helper->ask($input, $output, $question);
                    break;
                case 'int':
                    $question = new Question('Insert the default value of the new meta : ');
                    $value = $helper->ask($input, $output, $question);
                    if(!is_numeric($value)) {
                        $io->error('The value must be a number');
                        return Command::FAILURE;
                    }
                    break;
            }
            
        }

        $meta = new Meta();
        $meta->setName($name);
        $meta->setValue($value);
        $meta->setAllowed($allowed);

        $this->entityManager->persist($meta);
        $this->entityManager->flush();

        $io->success('Meta created successfully !');

        return Command::SUCCESS;
    }
}
