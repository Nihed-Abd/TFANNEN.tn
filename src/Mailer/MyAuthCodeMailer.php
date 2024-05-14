<?php
namespace App\Mailer;


use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
class MyAuthCodeMailer implements AuthCodeMailerInterface
{
    private \Symfony\Component\Mailer\MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        // Generate the authentication code
        $authCode = $user->getEmailAuthCode();

        // Create a Symfony Mime Email
        $email = (new Email())
            ->from("ilyess.saoudi@gmail.com")
            ->to($user->getEmail()) // Use the recipient's email from the user entity
            ->subject('Your Two-Factor Authentication Code')
            ->text('Your authentication code is: ' . $authCode); // Customize the email body as needed

        // Send the email
        $this->mailer->send($email);
    }
}
