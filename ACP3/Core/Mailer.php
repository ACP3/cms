<?php

namespace ACP3\Core;

use ACP3\Core\Mailer\MailerMessage;
use ACP3\Core\Mailer\MessageProcessor;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

/**
 * Class Email
 * @package ACP3\Core
 */
class Mailer
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SettingsInterface
     */
    private $config;
    /**
     * @var MessageProcessor
     */
    private $messageParser;

    /**
     * @var MailerMessage|null
     */
    private $message;
    /**
     * @var PHPMailer
     */
    private $phpMailer;

    /**
     * Mailer constructor.
     * @param LoggerInterface $logger
     * @param MessageProcessor $messageParser
     * @param SettingsInterface $config
     */
    public function __construct(
        LoggerInterface $logger,
        MessageProcessor $messageParser,
        SettingsInterface $config
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->messageParser = $messageParser;
    }

    /**
     * @param MailerMessage $data
     * @return $this
     */
    public function setData(MailerMessage $data)
    {
        $this->message = $data;

        return $this;
    }

    /**
     * Sends the email
     *
     * @return bool
     */
    public function send(): bool
    {
        try {
            if (!($this->message instanceof MailerMessage)) {
                throw new \InvalidArgumentException('No \MailerMessage given');
            }

            $this->configure();

            $this->addReplyTo();
            $this->addFrom();
            $this->addSender();

            $this->messageParser->process($this->phpMailer, $this->message);

            // Add attachments to the E-mail
            if (count($this->message->getAttachments()) > 0) {
                foreach ($this->message->getAttachments() as $attachment) {
                    if (!empty($attachment) && is_file($attachment)) {
                        $this->phpMailer->addAttachment($attachment);
                    }
                }
            }

            if (!empty($this->message->getRecipients())) {
                return $this->message->isBcc() === true ? $this->sendBcc() : $this->sendTo();
            }
        } catch (Exception $e) {
            $this->logger->error($e);
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return false;
    }

    private function addReplyTo()
    {
        $replyTo = $this->message->getReplyTo();

        if (is_array($replyTo) === true) {
            $this->phpMailer->addReplyTo($replyTo['email'], $replyTo['name']);
        } elseif (!empty($replyTo)) {
            $this->phpMailer->addReplyTo($replyTo);
        }
    }

    private function addFrom()
    {
        $from = $this->message->getFrom();
        if (is_array($from) === true) {
            $this->phpMailer->setFrom($from['email'], $from['name']);
        } else {
            $this->phpMailer->setFrom($from);
        }
    }

    private function addSender()
    {
        if (!empty($this->message->getSender())) {
            $this->phpMailer->Sender = $this->message->getSender();
        }
    }

    /**
     * Special sending logic for bcc only E-mails
     *
     * @return bool
     */
    private function sendBcc()
    {
        foreach ($this->message->getRecipients() as $recipient) {
            set_time_limit(10);

            $this->addRecipients($recipient, true);
        }

        return $this->phpMailer->send();
    }

    /**
     * Adds multiple recipients to the to be send email
     *
     * @param string|array $recipients
     * @param bool $bcc
     *
     * @return $this
     */
    private function addRecipients($recipients, $bcc = false)
    {
        if (is_array($recipients) === true) {
            if (isset($recipients['email'], $recipients['name']) === true) {
                $this->addRecipient($recipients['email'], $recipients['name'], $bcc);
            } else {
                foreach ($recipients as $recipient) {
                    if (is_array($recipient) === true) {
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
     * Adds a single recipient to the to be send email
     *
     * @param string $email
     * @param string $name
     * @param bool $bcc
     *
     * @return $this
     */
    private function addRecipient($email, $name = '', $bcc = false)
    {
        if ($bcc === true) {
            $this->phpMailer->addBCC($email, $name);
        } else {
            $this->phpMailer->addAddress($email, $name);
        }

        return $this;
    }

    /**
     * Special sending logic for E-mails without bcc addresses
     *
     * @return bool
     */
    private function sendTo(): bool
    {
        foreach ($this->message->getRecipients() as $recipient) {
            set_time_limit(20);
            $this->addRecipients($recipient);
            $this->phpMailer->send();
            $this->phpMailer->clearAllRecipients();
        }

        return true;
    }

    /**
     * Resets the currently set mailer values back to there default values
     *
     * @return $this
     */
    public function reset()
    {
        $this->message = null;

        if ($this->phpMailer) {
            $this->phpMailer->clearAllRecipients();
            $this->phpMailer->clearAttachments();
        }

        return $this;
    }

    /**
     * Initializes PHPMailer and sets the basic configuration parameters
     *
     * @return $this
     */
    private function configure()
    {
        if ($this->phpMailer === null) {
            $this->phpMailer = new PHPMailer(true);

            $settings = $this->config->getSettings(Schema::MODULE_NAME);

            if (strtolower($settings['mailer_type']) === 'smtp') {
                $this->phpMailer->set('Mailer', 'smtp');
                $this->phpMailer->Host = $settings['mailer_smtp_host'];
                $this->phpMailer->Port = $settings['mailer_smtp_port'];
                $this->phpMailer->SMTPSecure = in_array($settings['mailer_smtp_security'], ['ssl', 'tls'])
                    ? $settings['mailer_smtp_security']
                    : '';
                if ((bool)$settings['mailer_smtp_auth'] === true) {
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
