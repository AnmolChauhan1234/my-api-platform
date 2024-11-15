<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * @Route("/send-email", name="send_email")
     */
    public function sendEmail(): Response
    {
        $this->emailService->sendEmailAsync(
            'recipient@example.com',
            'Welcome to Symfony!',
            '<h1>Hello, Symfony User!</h1><p>Thank you for joining us.</p>'
        );

        return new Response('Email will be sent asynchronously!');
    }
}
