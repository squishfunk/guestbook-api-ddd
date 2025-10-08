<?php

namespace App\Command;

use App\Dto\GuestbookEntryInput;
use App\Entity\GuestbookEntry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:add-guestbook-entry',
    description: 'Add guestbook entry to the database',
)]
class AddGuestbookEntryCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Entry Nickname')
            ->addArgument('message', InputArgument::REQUIRED, 'Entry Message')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $entryDto = new GuestbookEntryInput();
        $entryDto->name = $input->getArgument('name');
        $entryDto->message = $input->getArgument('message');

        $errors = $this->validator->validate($entryDto);

        foreach($errors as $error){
            $io->error(sprintf('%s: %s', $error->getPropertyPath(), $error->getMessage()));
            return Command::FAILURE;
        }

        $guestbookEntry = new GuestbookEntry();
        $guestbookEntry->setName($entryDto->name);
        $guestbookEntry->setMessage($entryDto->message);

        $this->entityManager->persist($guestbookEntry);
        $this->entityManager->flush();

        $io->success(sprintf('Guestbook entry added! ID: %d', $guestbookEntry->getId()));

        return Command::SUCCESS;
    }
}
