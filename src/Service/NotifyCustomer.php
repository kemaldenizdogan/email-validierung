<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\EmailVerification;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NotifyCustomer implements NotifyCustomerInterface
{
    const TOKEN_LENGTH = 64;

    private $em;

    private $router;

    private $mailer;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $router, MailerInterface $mailer)
    {
        $this->em = $em;
        $this->router = $router;
        $this->mailer = $mailer;
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(self::TOKEN_LENGTH));
    }

    public function sendEmail(Customer $customer): bool
    {
        $token = $this->generateToken();

        $confirm_link = $this->router->generate('email_verification', [
            'token' => $token,
            'preference' => 1
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $reject_link = $this->router->generate('email_verification', [
            'token' => $token,
            'preference' => 0
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new TemplatedEmail())
            ->from('info@application.networker.info')
            ->to($customer->getEmail())
            ->subject('CRM E-Mail Validierung')
            ->htmlTemplate('emails/validation.html.twig')
            ->context([
                'name' => $customer->getName(),
                'confirm_link' => $confirm_link,
                'reject_link' => $reject_link,
            ]);

        try {
            $this->mailer->send($email);

            $emailVerification = new EmailVerification;
            $emailVerification->setCustomer($customer);
            $emailVerification->setToken($token);
            $emailVerification->setNotifiedAt(new DateTimeImmutable());
            $this->em->persist($emailVerification);
            $this->em->flush();
        } catch (TransportExceptionInterface $e) {
            // TODO : Send alert
            echo $e->getMessage();
            return false;
        }

        return true;
    }
}
