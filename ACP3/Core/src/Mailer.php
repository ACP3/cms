<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\Mailer\MailerMessage;
use InlineStyle\InlineStyle;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

class Mailer
{
    public function __construct(private PHPMailer $phpMailer, private LoggerInterface $logger, private View $view, private StringFormatter $stringFormatter)
    {
    }

    /**
     * Sends the email.
     */
    public function send(MailerMessage $message): bool
    {
        try {
            $this->phpMailer->Subject = $this->generateSubject($message);

            $this->addReplyTo($message);
            $this->addFrom($message);
            $this->addSender($message);
            $this->generateBody($message);

            // Add attachments to the E-mail
            foreach ($message->getAttachments() as $attachment) {
                if (!empty($attachment) && is_file($attachment)) {
                    $this->phpMailer->addAttachment($attachment);
                }
            }

            if (!empty($message->getRecipients())) {
                return $message->isBcc() === true ? $this->sendBcc($message) : $this->sendTo($message);
            }
        } catch (PHPMailerException|\Throwable $e) {
            $this->logger->error($e);
        }

        return false;
    }

    protected function generateSubject(MailerMessage $message): string
    {
        return $message->getSubject();
    }

    /**
     * @throws PHPMailerException
     */
    private function addReplyTo(MailerMessage $message): void
    {
        $replyTo = $message->getReplyTo();

        if (\is_array($replyTo) === true) {
            $this->phpMailer->addReplyTo($replyTo['email'], $replyTo['name']);
        } elseif (!empty($replyTo)) {
            $this->phpMailer->addReplyTo($replyTo);
        }
    }

    /**
     * @throws PHPMailerException
     */
    private function addFrom(MailerMessage $message): void
    {
        if (\is_array($message->getFrom()) === true) {
            $this->phpMailer->setFrom($message->getFrom()['email'], $message->getFrom()['name']);
        } else {
            $this->phpMailer->setFrom($message->getFrom());
        }
    }

    private function addSender(MailerMessage $message): void
    {
        if (!empty($message->getSender())) {
            $this->phpMailer->Sender = $message->getSender();
        }
    }

    /**
     * Generates the E-mail body.
     *
     * @throws PHPMailerException
     */
    private function generateBody(MailerMessage $message): void
    {
        if (!empty($message->getTemplate())) {
            $mail = [
                'charset' => 'UTF-8',
                'title' => $message->getSubject(),
                'body' => !empty($message->getHtmlBody()) ? $message->getHtmlBody() : $this->stringFormatter->nl2p(htmlspecialchars($message->getBody())),
                'signature' => $this->getHtmlSignature($message),
                'url_web_view' => $message->getUrlWeb(),
            ];
            $this->view->assign('mail', $mail);

            $htmlDocument = new InlineStyle($this->view->fetchTemplate($message->getTemplate()));
            /* @phpstan-ignore-next-line */
            $htmlDocument->applyStylesheet($htmlDocument->extractStylesheets());

            $this->phpMailer->msgHTML($htmlDocument->getHTML());

            // Fallback for E-mail clients which don't support HTML E-mails
            if (!empty($message->getBody())) {
                $this->phpMailer->AltBody = $this->decodeHtmlEntities($message->getBody() . $this->getTextSignature($message));
            } else {
                $this->phpMailer->AltBody = $this->phpMailer->html2text(
                    $message->getHtmlBody() . $this->getHtmlSignature($message),
                    true
                );
            }
        } else {
            $this->phpMailer->Body = $this->decodeHtmlEntities($message->getBody() . $this->getTextSignature($message));
        }
    }

    private function getHtmlSignature(MailerMessage $message): string
    {
        if (!empty($message->getMailSignature())) {
            if ($message->getMailSignature() === strip_tags($message->getMailSignature())) {
                return $this->stringFormatter->nl2p($message->getMailSignature());
            }

            return $message->getMailSignature();
        }

        return '';
    }

    private function decodeHtmlEntities(string $data): string
    {
        return html_entity_decode($data, ENT_QUOTES, 'UTF-8');
    }

    private function getTextSignature(MailerMessage $message): string
    {
        if (!empty($message->getMailSignature())) {
            return "\n-- \n" . $this->phpMailer->html2text($message->getMailSignature(), true);
        }

        return '';
    }

    /**
     * Special sending logic for bcc only E-mails.
     *
     * @throws PHPMailerException
     */
    private function sendBcc(MailerMessage $message): bool
    {
        if (\is_array($message->getRecipients()) === false || isset($message->getRecipients()['email']) === true) {
            $message->setRecipients([$message->getRecipients()]);
        }

        foreach ($message->getRecipients() as $recipient) {
            $this->addRecipients($recipient, true);
        }

        set_time_limit(10);

        $result = $this->phpMailer->send();

        $this->phpMailer->clearAllRecipients();

        return $result;
    }

    /**
     * Adds multiple recipients to the to be send email.
     *
     * @param array<array{email: string, name: string}>|string[]|string $recipients
     *
     * @throws PHPMailerException
     */
    private function addRecipients(array|string $recipients, bool $bcc = false): void
    {
        if (\is_array($recipients) === true) {
            if (isset($recipients['email'], $recipients['name']) === true) {
                $this->addRecipient($recipients['email'], $recipients['name'], $bcc);
            } else {
                foreach ($recipients as $recipient) {
                    if (\is_array($recipient) === true) {
                        $this->addRecipient($recipient['email'], $recipient['name'], $bcc);
                    } else {
                        $this->addRecipient($recipient, '', $bcc);
                    }
                }
            }
        } else {
            $this->addRecipient($recipients, '', $bcc);
        }
    }

    /**
     * Adds a single recipient to the to be send email.
     *
     * @throws PHPMailerException
     */
    private function addRecipient(string $email, string $name = '', bool $bcc = false): void
    {
        if ($bcc === true) {
            $this->phpMailer->addBCC($email, $name);
        } else {
            $this->phpMailer->addAddress($email, $name);
        }
    }

    /**
     * Special sending logic for E-mails without bcc addresses.
     *
     * @throws PHPMailerException
     */
    private function sendTo(MailerMessage $message): bool
    {
        if (\is_array($message->getRecipients()) === false || isset($message->getRecipients()['email']) === true) {
            $message->setRecipients([$message->getRecipients()]);
        }

        foreach ($message->getRecipients() as $recipient) {
            set_time_limit(20);
            $this->addRecipients($recipient);
            $this->phpMailer->send();
            $this->phpMailer->clearAllRecipients();
        }

        return true;
    }

    /**
     * Resets the currently set mailer values back to their default values.
     */
    public function reset(): self
    {
        $this->phpMailer->clearAllRecipients();
        $this->phpMailer->clearAttachments();

        return $this;
    }
}
