<?php

namespace App\Command;

use App\Service\ImportFileInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

class ImportFileCommand extends Command
{
    protected static $defaultName = 'import:file';
    protected static $defaultDescription = 'File import command';

    private $importFile;

    private $projectDir;

    public function __construct(ImportFileInterface $importFile, KernelInterface $kernel)
    {
        parent::__construct();

        $this->importFile = $importFile;
        $this->projectDir = $kernel->getProjectDir();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('fileName', InputArgument::REQUIRED, 'File name (CRM-Adresse)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fileName = $input->getArgument('fileName');

        switch ($fileName) {
            case 'CRM-Adresse':
                if ($this->importFile->load($this->projectDir . '/var/storage/CRM-Adresse.csv')->insert()) {
                    $io->success('File successfully imported.');
                } else {
                    $io->note('An unexpected error occurred while importing the file!');
                }
                break;
            default:
                $io->note('Undefined file type!');
                break;
        }

        return Command::SUCCESS;
    }
}
