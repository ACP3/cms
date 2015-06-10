<?php
namespace ACP3\Core\Helpers;

use ACP3\Core;

/**
 * Class SendEmail
 * @package ACP3\Core\Helpers
 */
class SendEmail
{
    /**
     * @var \ACP3\Core\Mailer
     */
    protected $mailer;

    /**
     * @param \ACP3\Core\Mailer $mailer
     */
    public function __construct(Core\Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Generates and sends an E-mail
     *
     * @param $recipientName
     * @param $recipientEmail
     * @param $from
     * @param $subject
     * @param $body
     * @param string $mailSignature
     * @return mixed
     */
    public function execute($recipientName, $recipientEmail, $from, $subject, $body, $mailSignature = '')
    {
        if (!empty($recipientName)) {
            $to = [
                'name' => $recipientName,
                'email' => $recipientEmail
            ];
        } else {
            $to = $recipientEmail;
        }

        return $this->mailer
            ->reset()
            ->setSubject($subject)
            ->setBody($body)
            ->setMailSignature($mailSignature)
            ->setFrom($from)
            ->setRecipients($to)
            ->send();
    }
}
