<?php

namespace App\Command;

use App\Entity\Customer;
use App\Service\NotifyCustomerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class NotifyCustomersCommand extends Command
{
    protected static $defaultName = 'notify:customers';
    protected static $defaultDescription = 'Notify customers command';

    private $em;

    private $notifyCustomer;

    public function __construct(EntityManagerInterface $em, NotifyCustomerInterface $notifyCustomer)
    {
        parent::__construct();

        $this->em = $em;
        $this->notifyCustomer = $notifyCustomer;
    }

    protected function configure(): void
    {
        $this
            ->addOption('max', null, InputOption::VALUE_REQUIRED, 'Maxium notification count especially for testing');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $customers = $this->em->getRepository(Customer::class)->findNotifiableCustomers();

        for ($i = 0; $i < count($customers); $i++) {
            if ($this->notifyCustomer->sendEmail($customers[$i])) {
                $io->success(sprintf('Notification successfully sent to %s email address.', $customers[$i]->getEmail()));
            } else {
                $io->note(sprintf('Notification failed for %s email address!', $customers[$i]->getEmail()));
            }

            if (!empty($input->getOption('max')) && $i + 1 == (int) $input->getOption('max')) {
                break;
            }
        }

        return Command::SUCCESS;
    }
}
