<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email; // Make sure to import this class
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class EmailService
{
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function sendEmailAsync(string $to, string $subject, string $htmlContent)
    {
        // Create the email object
        $email = (new Email())
            ->from('postmaster@sandboxd9f6e065899d4789b3796edfced8ebe6.mailgun.org')
            ->to($to)                       // Recipient's email
            ->subject($subject)             // Email subject
            ->html($htmlContent);           // HTML content

        // Dispatch the email asynchronously using the MessageBus
        $this->bus->dispatch(new SendEmailMessage($email));
    }
}
