<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\View\Block\Admin;


use ACP3\Core\Helpers\Date;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractSettingsFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Guestbook\Installer\Schema;

class GuestbookSettingsFormBlock extends AbstractSettingsFormBlock
{
    /**
     * @var Date
     */
    private $date;
    /**
     * @var Modules
     */
    private $modules;

    /**
     * GuestbookSettingsFormBlock constructor.
     *
     * @param FormBlockContext $context
     * @param SettingsInterface $settings
     * @param Date $date
     * @param Modules $modules
     */
    public function __construct(
        FormBlockContext $context,
        SettingsInterface $settings,
        Date $date,
        Modules $modules
    ) {
        parent::__construct($context, $settings);

        $this->date = $date;
        $this->modules = $modules;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $settings = $this->getData();

        $notificationTypes = [
            0 => $this->translator->t('guestbook', 'no_notification'),
            1 => $this->translator->t('guestbook', 'notify_on_new_entry'),
            2 => $this->translator->t('guestbook', 'notify_and_enable')
        ];

        return [
            'dateformat' => $this->date->dateFormatDropdown($settings['dateformat']),
            'notify' => $this->forms->choicesGenerator('notify', $notificationTypes, $settings['notify']),
            'overlay' => $this->forms->yesNoCheckboxGenerator('overlay', $settings['overlay']),
            'form' => array_merge($settings, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
            'options' => $this->fetchOptions($settings)
        ];
    }

    /**
     * @param array $settings
     * @return array
     */
    private function fetchOptions(array $settings): array
    {
        $options = [];
        if ($this->modules->isActive('emoticons') === true) {
            $options['allow_emoticons'] = $this->forms->yesNoCheckboxGenerator(
                'emoticons',
                $settings['emoticons']
            );
        }

        if ($this->modules->isActive('newsletter') === true) {
            $options['newsletter_integration'] = $this->forms->yesNoCheckboxGenerator(
                'newsletter_integration',
                $settings['newsletter_integration']
            );
        }

        return $options;
    }

    /**
     * @inheritdoc
     */
    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }
}
