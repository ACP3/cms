<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\EventListener;

use ACP3\Core\Helpers\SendEmail;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Mailer\MailerMessage;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SendNotificationEmailOnGuestbookModelAfterSaveListener implements EventSubscriberInterface
{
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Core\Helpers\SendEmail
     */
    private $sendEmail;

    public function __construct(
        RouterInterface $router,
        SendEmail $sendEmail,
        SettingsInterface $settings,
        Translator $translator)
    {
        $this->settings = $settings;
        $this->translator = $translator;
        $this->router = $router;
        $this->sendEmail = $sendEmail;
    }

    public function __invoke(ModelSaveEvent $event)
    {
        if (!$event->isIsNewEntry()) {
            return;
        }

        $guestbookSettings = $this->settings->getSettings(Schema::MODULE_NAME);

        $fullPath = $this->router->route('guestbook', true) . '#gb-entry-' . $event->getEntryId();
        $body = sprintf(
            $guestbookSettings['notify'] == 1
                ? $this->translator->t('guestbook', 'notification_email_body_1')
                : $this->translator->t('guestbook', 'notification_email_body_2'),
            $this->router->route('', true),
            $fullPath
        );

        $message = (new MailerMessage())
            ->setRecipients($guestbookSettings['notify_email'])
            ->setFrom($guestbookSettings['notify_email'])
            ->setSubject($this->translator->t('guestbook', 'notification_email_subject'))
            ->setBody($body);
        $this->sendEmail->execute($message);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'guestbook.model.guestbook.after_save' => '__invoke',
        ];
    }
}
