<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\Mailer;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use PHPMailer\PHPMailer\PHPMailer;

class MailerConfigurator
{
    public function __construct(private readonly SettingsInterface $settings)
    {
    }

    public function configure(PHPMailer $phpMailer): void
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        if (strtolower((string) $settings['mailer_type']) === 'smtp') {
            $phpMailer->set('Mailer', 'smtp');
            $phpMailer->Host = $settings['mailer_smtp_host'];
            $phpMailer->Port = $settings['mailer_smtp_port'];
            $phpMailer->SMTPSecure = \in_array($settings['mailer_smtp_security'], ['ssl', 'tls'])
                ? $settings['mailer_smtp_security']
                : '';
            if ((bool) $settings['mailer_smtp_auth'] === true) {
                $phpMailer->SMTPAuth = true;
                $phpMailer->Username = $settings['mailer_smtp_user'];
                $phpMailer->Password = $settings['mailer_smtp_password'];
            }
        } else {
            $phpMailer->set('Mailer', 'mail');
        }
        $phpMailer->CharSet = 'UTF-8';
        $phpMailer->Encoding = 'quoted-printable';
        $phpMailer->WordWrap = PHPMailer::STD_LINE_LENGTH;
    }
}
