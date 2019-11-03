<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

use ACP3\Core\Helpers\StringFormatter;
use ACP3\Core\Mailer\MailerMessage;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use InlineStyle\InlineStyle;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

class Mailer
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var SettingsInterface
     */
    protected $config;
    /**
     * @var \ACP3\Core\Helpers\StringFormatter
     */
    protected $stringFormatter;
    /**
     * @var string
     */
    private $subject = '';
    /**
     * @var string
     */
    private $body = '';
    /**
     * @var string
     */
    private $htmlBody = '';
    /**
     * @var string
     */
    private $urlWeb = '';
    /**
     * @var string
     */
    private $mailSignature = '';
    /**
     * @var string|array
     */
    private $from;
    /**
     * @var string|array
     */
    private $recipients;
    /**
     * @var bool
     */
    private $bcc = false;
    /**
     * @var array
     */
    private $attachments = [];
    /**
     * @var string
     */
    private $template = '';
    /**
     * @var MailerMessage|null
     */
    private $mailerMessage;
    /**
     * @var PHPMailer
     */
    private $phpMailer;

    /**
     * Mailer constructor.
     */
    public function __construct(
        LoggerInterface $logger,
        View $view,
        SettingsInterface $config,
        StringFormatter $stringFormatter
    ) {
        $this->logger = $logger;
        $this->view = $view;
        $this->config = $config;
        $this->stringFormatter = $stringFormatter;
    }

    /**
     * @param string|array $from
     *
     * @return $this
     *
     * @deprecated since version 4.8.0, to be removed with version 5.0.0
     */
    public function setFrom($from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param string $mailSignature
     *
     * @return $this
     *
     * @deprecated since version 4.8.0, to be removed with version 5.0.0
     */
    public function setMailSignature($mailSignature): self
    {
        $this->mailSignature = $mailSignature;

        return $this;
    }

    /**
     * @param string $htmlText
     *
     * @return $this
     *
     * @deprecated since version 4.8.0, to be removed with version 5.0.0
     */
    public function setHtmlBody($htmlText): self
    {
        $this->htmlBody = $htmlText;

        return $this;
    }

    /**
     * @param string $urlWeb
     *
     * @return $this
     *
     * @deprecated since version 4.8.0, to be removed with version 5.0.0
     */
    public function setUrlWeb($urlWeb): self
    {
        $this->urlWeb = $urlWeb;

        return $this;
    }

    /**
     * @param bool $bcc
     *
     * @return $this
     *
     * @deprecated since version 4.8.0, to be removed with version 5.0.0
     */
    public function setBcc($bcc): self
    {
        $this->bcc = (bool) $bcc;

        return $this;
    }

    /**
     * @param string $subject
     *
     * @return $this
     *
     * @deprecated since version 4.8.0, to be removed with version 5.0.0
     */
    public function setSubject($subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @param string $body
     *
     * @return $this
     *
     * @deprecated since version 4.8.0, to be removed with version 5.0.0
     */
    public function setBody($body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param array|string $recipients
     *
     * @return $this
     *
     * @deprecated since version 4.8.0, to be removed with version 5.0.0
     */
    public function setRecipients($recipients): self
    {
        $this->recipients = $recipients;

        return $this;
    }

    /**
     * @param string|array $attachments
     *
     * @return $this
     *
     * @deprecated since version 4.8.0, to be removed with version 5.0.0
     */
    public function setAttachments($attachments): self
    {
        if (\is_array($attachments)) {
            $this->attachments = $attachments;
        } else {
            $this->attachments[] = $attachments;
        }

        return $this;
    }

    /**
     * @param string $template
     *
     * @return $this
     *
     * @deprecated since version 4.8.0, to be removed with version 5.0.0
     */
    public function setTemplate($template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return $this
     *
     * @deprecated since version 4.30.0, to be removed with version 5.0.0
     */
    public function setData(MailerMessage $data): self
    {
        $this->mailerMessage = $data;

        return $this;
    }

    /**
     * Sends the email.
     */
    public function send(?MailerMessage $message = null): bool
    {
        $message = $this->createMailerMessage($message);

        try {
            $this->configure();

            $this->phpMailer->Subject = $this->generateSubject($message);

            $this->addReplyTo($message);
            $this->addFrom($message);
            $this->addSender($message);
            $this->generateBody($message);

            // Add attachments to the E-mail
            foreach ($message->getAttachments() as $attachment) {
                if (!empty($attachment) && \is_file($attachment)) {
                    $this->phpMailer->addAttachment($attachment);
                }
            }

            if (!empty($message->getRecipients())) {
                return $message->isBcc() === true ? $this->sendBcc($message) : $this->sendTo($message);
            }
        } catch (PHPMailerException $e) {
            $this->logger->error($e);
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return false;
    }

    private function createMailerMessage(?MailerMessage $message): MailerMessage
    {
        if ($message === null) {
            if ($this->mailerMessage instanceof MailerMessage) {
                $message = $this->mailerMessage;
            } else {
                $message = (new MailerMessage())
                    ->setAttachments($this->attachments)
                    ->setSubject($this->subject)
                    ->setBody($this->body)
                    ->setHtmlBody($this->htmlBody)
                    ->setMailSignature($this->mailSignature)
                    ->setRecipients($this->recipients)
                    ->setTemplate($this->template)
                    ->setUrlWeb($this->urlWeb)
                    ->setFrom($this->from);
            }
        }

        return $message;
    }

    protected function generateSubject(MailerMessage $message): string
    {
        return $message->getSubject();
    }

    /**
     * @throws \PHPMailer\PHPMailer\Exception
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
     * @throws \PHPMailer\PHPMailer\Exception
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
     * @return $this
     */
    private function generateBody(MailerMessage $message): self
    {
        if (!empty($message->getTemplate())) {
            $mail = [
                'charset' => 'UTF-8',
                'title' => $message->getSubject(),
                'body' => !empty($message->getHtmlBody()) ? $message->getHtmlBody() : $this->stringFormatter->nl2p(\htmlspecialchars($message->getBody())),
                'signature' => $this->getHtmlSignature($message),
                'url_web_view' => $message->getUrlWeb(),
            ];
            $this->view->assign('mail', $mail);

            $htmlDocument = new InlineStyle($this->view->fetchTemplate($message->getTemplate()));
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

        return $this;
    }

    private function getHtmlSignature(MailerMessage $message): string
    {
        if (!empty($message->getMailSignature())) {
            if ($message->getMailSignature() === \strip_tags($message->getMailSignature())) {
                return $this->stringFormatter->nl2p($message->getMailSignature());
            }

            return $message->getMailSignature();
        }

        return '';
    }

    /**
     * @param string $data
     */
    private function decodeHtmlEntities($data): string
    {
        return \html_entity_decode($data, ENT_QUOTES, 'UTF-8');
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
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendBcc(MailerMessage $message): bool
    {
        if (\is_array($message->getRecipients()) === false || isset($message->getRecipients()['email']) === true) {
            $message->setRecipients([$message->getRecipients()]);
        }

        foreach ($message->getRecipients() as $recipient) {
            \set_time_limit(10);

            $this->addRecipients($recipient, true);
        }

        return $this->phpMailer->send();
    }

    /**
     * Adds multiple recipients to the to be send email.
     *
     * @param string|array $recipients
     * @param bool         $bcc
     *
     * @return $this
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function addRecipients($recipients, $bcc = false): self
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

        return $this;
    }

    /**
     * Adds a single recipient to the to be send email.
     *
     * @param string $email
     * @param string $name
     * @param bool   $bcc
     *
     * @return $this
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function addRecipient($email, $name = '', $bcc = false): self
    {
        if ($bcc === true) {
            $this->phpMailer->addBCC($email, $name);
        } else {
            $this->phpMailer->addAddress($email, $name);
        }

        return $this;
    }

    /**
     * Special sending logic for E-mails without bcc addresses.
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendTo(MailerMessage $message): bool
    {
        if (\is_array($message->getRecipients()) === false || isset($message->getRecipients()['email']) === true) {
            $message->setRecipients([$message->getRecipients()]);
        }

        foreach ($message->getRecipients() as $recipient) {
            \set_time_limit(20);
            $this->addRecipients($recipient);
            $this->phpMailer->send();
            $this->phpMailer->clearAllRecipients();
        }

        return true;
    }

    /**
     * Resets the currently set mailer values back to their default values.
     *
     * @return $this
     */
    public function reset(): self
    {
        $this->subject = '';
        $this->body = '';
        $this->htmlBody = '';
        $this->urlWeb = '';
        $this->mailSignature = '';
        $this->from = '';
        $this->recipients = null;
        $this->bcc = false;
        $this->attachments = [];
        $this->template = '';

        if ($this->phpMailer) {
            $this->phpMailer->clearAllRecipients();
            $this->phpMailer->clearAttachments();
        }

        return $this;
    }

    /**
     * Initializes PHPMailer and sets the basic configuration parameters.
     *
     * @return $this
     */
    private function configure(): self
    {
        if ($this->phpMailer === null) {
            $this->phpMailer = new PHPMailer(true);

            $settings = $this->config->getSettings(Schema::MODULE_NAME);

            if (\strtolower($settings['mailer_type']) === 'smtp') {
                $this->phpMailer->set('Mailer', 'smtp');
                $this->phpMailer->Host = $settings['mailer_smtp_host'];
                $this->phpMailer->Port = $settings['mailer_smtp_port'];
                $this->phpMailer->SMTPSecure = \in_array($settings['mailer_smtp_security'], ['ssl', 'tls'])
                    ? $settings['mailer_smtp_security']
                    : '';
                if ((bool) $settings['mailer_smtp_auth'] === true) {
                    $this->phpMailer->SMTPAuth = true;
                    $this->phpMailer->Username = $settings['mailer_smtp_user'];
                    $this->phpMailer->Password = $settings['mailer_smtp_password'];
                }
            } else {
                $this->phpMailer->set('Mailer', 'mail');
            }
            $this->phpMailer->CharSet = 'UTF-8';
            $this->phpMailer->Encoding = 'quoted-printable';
            $this->phpMailer->WordWrap = PHPMailer::STD_LINE_LENGTH;
        }

        return $this;
    }
}
