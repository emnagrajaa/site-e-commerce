<?php

namespace App\Service;

use App\Entity\Order;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Psr\Log\LoggerInterface;

class EmailService
{
    private $mailer;
    private $twig;
    private $logger;

    public function __construct(MailerInterface $mailer, Environment $twig, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function sendOrderConfirmation(string $to, Order $order): void
    {
        try {
            $this->logger->info('Début de l\'envoi d\'e-mail à : ' . $to . ' pour la commande #' . $order->getId());
            $email = (new Email())
                ->from('alaguizani62@gmail.com') // Doit correspondre à MAILER_DSN
                ->to($to)
                ->subject('Confirmation de votre commande #' . $order->getId())
                ->html(
                    $this->twig->render('order/confirmation.html.twig', ['order' => $order])
                );

            $this->mailer->send($email);
            $this->logger->info('E-mail envoyé avec succès à : ' . $to);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('Erreur inattendue lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
            throw $e;
        }
    }
}