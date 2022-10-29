<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\EventListener;

use ACP3\Core\Helpers\SendEmail;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Mailer\MailerMessage;
use ACP3\Core\Model\Event\AfterModelSaveEvent;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\Users\Installer\Schema as UsersSchema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SendRegistrationMailListener implements EventSubscriberInterface
{
    public function __construct(private readonly RequestInterface $request, private readonly SendEmail $sendEmail, private readonly SettingsInterface $settings, private readonly Translator $translator)
    {
    }

    public function __invoke(AfterModelSaveEvent $event): void
    {
        if (!$event->isIsNewEntry()) {
            return;
        }

        $data = $event->getData();

        $systemSettings = $this->settings->getSettings(Schema::MODULE_NAME);
        $settings = $this->settings->getSettings(UsersSchema::MODULE_NAME);

        $subject = $this->translator->t(
            'users',
            'register_mail_subject',
            [
                '{title}' => $systemSettings['site_title'],
                '{host}' => $this->request->getHost(),
            ]
        );
        $body = $this->translator->t(
            'users',
            'register_mail_message',
            [
                '{name}' => $data['nickname'],
                '{mail}' => $data['mail'],
                '{password}' => $data['pwd'],
                '{title}' => $systemSettings['site_title'],
                '{host}' => $this->request->getHost(),
            ]
        );

        $data = (new MailerMessage())
            ->setRecipients($data['mail'])
            ->setFrom($settings['mail'])
            ->setSubject($subject)
            ->setBody($body)
            ->setTemplate('Users/layout.email.register.tpl');

        $this->sendEmail->execute($data);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'users.model.users.after_save' => '__invoke',
        ];
    }
}
