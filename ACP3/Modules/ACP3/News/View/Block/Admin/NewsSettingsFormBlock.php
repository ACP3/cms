<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\News\View\Block\Admin;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractSettingsFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\News\Installer\Schema;

class NewsSettingsFormBlock extends AbstractSettingsFormBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var Date
     */
    private $dateHelper;

    /**
     * NewsSettingsFormBlock constructor.
     * @param FormBlockContext $context
     * @param SettingsInterface $settings
     * @param Modules $modules
     * @param Date $dateHelper
     */
    public function __construct(
        FormBlockContext $context,
        SettingsInterface $settings,
        Modules $modules,
        Date $dateHelper
    ) {
        parent::__construct($context, $settings);

        $this->settings = $settings;
        $this->modules = $modules;
        $this->dateHelper = $dateHelper;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $settings = $this->getData();

        return [
            'dateformat' => $this->dateHelper->dateFormatDropdown($settings['dateformat']),
            'readmore' => $this->forms->yesNoCheckboxGenerator('readmore', $settings['readmore']),
            'form' => \array_merge($settings, $this->getRequestData()),
            'sidebar_entries' => $this->forms->recordsPerPage((int)$settings['sidebar'], 1, 10, 'sidebar'),
            'category_in_breadcrumb' => $this->forms->yesNoCheckboxGenerator(
                'category_in_breadcrumb',
                $settings['category_in_breadcrumb']
            ),
            'form_token' => $this->formToken->renderFormToken(),
            'allow_comments' => $this->fetchOptions($settings),
        ];
    }

    /**
     * @param array $settings
     * @return array
     */
    private function fetchOptions(array $settings): array
    {
        if ($this->modules->isActive('comments') === true) {
            return $this->forms->yesNoCheckboxGenerator('comments', $settings['comments']);
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }
}
