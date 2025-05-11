<?php

namespace App\Service;

use App\Entity\Order;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendOrderConfirmation(string $to, Order $order): void
    {
        $email = (new Email())
            ->from('no-reply@artisanat-boutique.com')
            ->to($to)
            ->subject('Confirmation de votre commande #' . $order->getId())
            ->text('Votre commande #' . $order->getId() . ' a été confirmée. Total: ' . $order->getTotal() . '€');

        $this->mailer->send($email);
    }
}