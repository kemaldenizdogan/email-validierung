<?php

namespace App\Controller;

use App\Entity\Customer;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Writer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    /**
     * @Route("/export/csv", name="export_csv")
     */
    public function exportCsv(EntityManagerInterface $em): Response
    {
        $notifiedCustomers = $em->getRepository(Customer::class)->findNotifiedCustomers();

        $header = ['Name', 'E-Mail Adresse', 'Benachrichtigungsdatum', 'Antwortdatum', 'Vorzug'];

        $records = [];

        foreach ($notifiedCustomers as $data) {
            $records[] = [
                $data['name'],
                $data['email'],
                $data['notified_at']->setTimezone(new DateTimeZone('Europe/Berlin'))->format('H:i:s d.m.Y'),
                $data['verified_at']->setTimezone(new DateTimeZone('Europe/Berlin'))->format('H:i:s d.m.Y'),
                $data['verification_type'] ? 'Ja' : 'Nein',
            ];
        }

        $csv = Writer::createFromString();
        $csv->insertOne($header);
        $csv->insertAll($records);

        $response = new Response($csv->toString());
        $response->headers->set('Content-Type', 'text/csv');
        return $response;
    }
}
