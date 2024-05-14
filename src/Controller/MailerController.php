<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    #[Route('/mailer', name: 'app_mailer')]
    public function index(): Response
    {
        return $this->render('mailer/index.html.twig', [
            'controller_name' => 'MailerController',
        ]);
    }

    #[Route('/fr/email', name: 'send_email')]
    public function sendEmail(
        string $subject= '',
        string $to = 'junelamelon92@gmail.com',
        string $content = ''
    ): Response {
        $email = (new Email())
            ->from('junelamelon@gmail.com')
            ->to($to)
            ->subject($subject)
            ->html($content);

        try {
            $this->mailer->send($email);
            return new Response('Mail envoyé avec succès.');
        } catch (TransportExceptionInterface $e) {
            return new Response('Erreur lors de l\'envoi du mail.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
