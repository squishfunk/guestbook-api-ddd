<?php

namespace App\User\Application\EventHandler;

use App\User\Domain\Event\UserRegisteredEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
final class SendConfirmationEmailOnUserRegistered
{

    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator
    )
    {}

    public function __invoke(UserRegisteredEvent $event): void
    {

        $verificationUrl = $this->urlGenerator->generate(
            'authverify_email',
            ['token' => $event->emailVerificationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );


        $email = (new Email())
            ->from('aplication@gmail.com')
            ->to($event->email())
            ->subject('Confirm your email address')
            ->text(sprintf(
                'Hello %s! Thank you for registering. To confirm your email address, please click on the following link: %s',
                $event->name(),
                $verificationUrl
            ))
            ->html(sprintf(
                '<h2>Welcome %s!</h2>
        <p>Thank you for registering with our application.</p>
        <p>To confirm your email address, please click the button below:</p>
        <a href="%s" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Confirm Email</a>
        <p>Or copy and paste this link into your browser:</p>
        <p><a href="%s">%s</a></p>
        <p>If you did not register with our application, please ignore this email.</p>
        <p>Best regards,<br>The Team</p>',
                htmlspecialchars($event->name()),
                $verificationUrl,
                $verificationUrl,
                $verificationUrl
            ));

        $this->mailer->send($email);



        // For now, just log that verification email should be sent
        error_log(sprintf(
            'Email verification should be sent to: %s for user: %s with token: %s',
            $event->email(),
            $event->userId(),
            $event->emailVerificationToken()
        ));
    }
}
