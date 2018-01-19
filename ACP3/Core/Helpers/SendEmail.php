<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core;

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
     * Generates and sends an E-mail.
     *
     * @param string|Core\Mailer\MailerMessage $recipientName
     * @param string                           $recipientEmail
     * @param string                           $from
     * @param string                           $subject
     * @param string                           $body
     * @param string                           $mailSignature
     *
     * @return bool
     *
     * @deprecated since version 4.8.0, to be removed with version 5.0.0. Use the 'core.mailer' service directly instead
     */
    public function execute(
        $recipientName,
        $recipientEmail = '',
        $from = '',
        $subject = '',
        $body = '',
        $mailSignature = ''
    ) {
        if ($recipientName instanceof Core\Mailer\MailerMessage) {
            return $this->mailer
                ->reset()
                ->setData($recipientName)
                ->send();
        }

        if (!empty($recipientName)) {
            $to = [
                'name' => $recipientName,
                'email' => $recipientEmail,
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
