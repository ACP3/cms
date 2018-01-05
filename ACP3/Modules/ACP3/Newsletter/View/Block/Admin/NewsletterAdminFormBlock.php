<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\View\Block\Admin;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractRepositoryAwareFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\NewslettersRepository;

class NewsletterAdminFormBlock extends AbstractRepositoryAwareFormBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * NewsletterFormBlock constructor.
     * @param FormBlockContext $context
     * @param NewslettersRepository $newslettersRepository
     * @param SettingsInterface $settings
     */
    public function __construct(
        FormBlockContext $context,
        NewslettersRepository $newslettersRepository,
        SettingsInterface $settings
    ) {
        parent::__construct($context, $newslettersRepository);

        $this->settings = $settings;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $newsletter = $this->getData();

        $this->title->setPageTitlePrefix($newsletter['title']);

        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $actions = [
            1 => $this->translator->t('newsletter', 'send_and_save'),
            0 => $this->translator->t('newsletter', 'only_save'),
        ];

        return [
            'settings' => !empty($newsletter['html'])
                ? \array_merge($settings, ['html' => $newsletter['html']])
                : $settings,
            'test' => $this->forms->yesNoCheckboxGenerator('test', 0),
            'action' => $this->forms->checkboxGenerator('action', $actions, 1),
            'form' => \array_merge($newsletter, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return [
            'title' => '',
            'text' => '',
            'date' => '',
        ];
    }
}
