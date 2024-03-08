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
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'swiftchat:init:meta',
    description: 'Add a short description for your command',
    aliases: ['s:i:m']
)]
class SwiftchatInitMetaCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $defaults = [
            [
                'name' => 'language',
                'value' => 'fr',
                'allowed' => 'string'
            ],
            [
                'name' => 'allow-friend-request',
                'value' => 'true',
                'allowed' => 'bool'
            ],
            [
                'name' => 'allow-easter',
                'value' => 'true',
                'allowed' => 'bool'
            ]
        ];

        foreach($defaults as $default) {
            $meta = new Meta();
            $meta->setName($default['name']);
            $meta->setValue($default['value']);
            $meta->setAllowed($default['allowed']);
            $this->entityManager->persist($meta);
        }

        $this->entityManager->flush();

        $io->success('Meta data has been saved successfully!');

        return Command::SUCCESS;
    }
}
