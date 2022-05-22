<?php

namespace App\Controller;

use App\Entity\EmailVerification;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EmailVerificationController extends AbstractController
{
    /**
     * @Route("/email/verification", name="email_verification")
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->query->get('token');
        $verification_type = is_numeric($request->query->get('preference')) ? (int) $request->query->get('preference') : null;

        if (!in_array($verification_type, [0, 1])) {
            throw new AccessDeniedHttpException('ungültige Anfrage!');
        }

        $emailVerification = $em->getRepository(EmailVerification::class)->findOneBy([
            'token' => $token,
            'verification_type' => null
        ]);

        if (empty($emailVerification)) {
            throw new AccessDeniedHttpException('Sie sind nicht zugriffsberechtigt!');
        }

        $emailVerification->setVerifiedAt(new DateTimeImmutable());
        $emailVerification->setVerificationType($verification_type);
        $em->persist($emailVerification);
        $em->flush();

        return $this->render('email_verification/index.html.twig', [
            'name' => $emailVerification->getCustomer()->getName(),
            'verification_type' => $verification_type,
        ]);
    }
}
