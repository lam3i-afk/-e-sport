<?php

namespace App\Notification;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

/**
 * Notification envoyée au owner d'une équipe quand un membre demande à rejoindre.
 */
class JoinRequestNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        private string $requesterName,
        private string $teamName,
        private string $projectDir = '',
    ) {
        parent::__construct(
            subject: sprintf('Nouvelle demande pour rejoindre %s', $teamName)
        );

        $this->importance(Notification::IMPORTANCE_HIGH);
    }

    public function getContent(): string
    {
        return sprintf(
            '%s souhaite rejoindre votre équipe "%s". Consultez votre dashboard pour accepter ou refuser.',
            $this->requesterName,
            $this->teamName
        );
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $email = (new TemplatedEmail())
            ->from(new Address('rajhiaziz2@gmail.com', 'E-Sports Platform'))
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
            ->htmlTemplate('notification/join_request.html.twig')
            ->context([
                'requesterName' => $this->requesterName,
                'teamName' => $this->teamName,
            ]);

        // Embed site logo as inline attachment
        $logoPath = $this->projectDir . '/public/img/Black_and_Green_Illustrative_E-Sports_Gaming_Logo__3_-removebg-preview.png';
        if (file_exists($logoPath)) {
            $email->embedFromPath($logoPath, 'logo', 'image/png');
        }

        return new EmailMessage($email);
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        return ['email'];
    }
}
