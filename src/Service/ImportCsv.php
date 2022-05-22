<?php

namespace App\Service;

use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;

class ImportCsv implements ImportFileInterface
{
    private $em;

    private $records;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function load(string $filePath): self
    {
        $file = Reader::createFromPath($filePath, 'r');
        $file->setDelimiter(';');
        $file->setHeaderOffset(0);

        $this->records = $file->getRecords();

        return $this;
    }

    public function insert(): bool
    {
        if (!is_array($this->records) && empty($this->records)) {
            return false;
        }

        $this->em->getRepository(Customer::class)->truncate();

        foreach ($this->records as $row) {
            $status = 1;

            if (!mb_detect_encoding($row['Name']) || !mb_detect_encoding($row['E-Mail Adresse'])) {
                $row['Name'] = iconv('UTF-8', 'ASCII//IGNORE', $row['E-Mail Adresse']);
                $row['E-Mail Adresse'] = iconv('UTF-8', 'ASCII//IGNORE', $row['E-Mail Adresse']);
                $status = 0;
            }

            $customer = new Customer;
            $customer->setName(trim($row['Name']));
            $customer->setEmail(trim($row['E-Mail Adresse']));
            $customer->setStatus($status);
            $this->em->persist($customer);
        }

        $this->em->flush();
        $this->em->clear();

        return true;
    }
}
