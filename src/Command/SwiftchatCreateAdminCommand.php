<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'swiftchat:create:admin',
    description: 'Create an Admin Account',
    aliases: ['s:c:a']
)]
class SwiftchatCreateAdminCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Insert the email address of the new admin')
            ->addArgument('firstname', InputArgument::OPTIONAL, 'Insert the firstname of the new admin')
            ->addArgument('lastname', InputArgument::OPTIONAL, 'Insert the lastname of the new admin')
            ->addArgument('password', InputArgument::OPTIONAL, 'Insert the password of the new admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);

        $email = $input->getArgument('email');
        if(!$email) {
            $question = new Question('Insert the email address of the new admin : ');
            $email = $helper->ask($input, $output, $question);
        }
        $firstname = $input->getArgument('firstname');
        if(!$firstname) {
            $question = new Question('Insert the firstname of the new admin : ');
            $firstname = $helper->ask($input, $output, $question);
        }

        $lastname = $input->getArgument('lastname');
        if(!$lastname) {
            $question = new Question('Insert the lastname of the new admin : ');
            $lastname = $helper->ask($input, $output, $question);
        }

        $password = $input->getArgument('password');
        if(!$password) {
            $question = new Question('Insert the password of the new admin : ');
            $password = $helper->ask($input, $output, $question);
        }


        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setPlainPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Admin account created successfully !');

        return Command::SUCCESS;
    }
}
