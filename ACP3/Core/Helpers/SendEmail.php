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
     * Generates and sends an E-mail
     *
     * @param string|Core\Mailer\MailerMessage $messageData
     * @return bool
     */
    public function execute(Core\Mailer\MailerMessage $messageData)
    {
        return $this->mailer
            ->reset()
            ->setData($messageData)
            ->send();
    }
}
