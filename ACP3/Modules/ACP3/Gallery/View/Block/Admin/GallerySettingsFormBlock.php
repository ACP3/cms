<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\View\Block\Admin;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractSettingsFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Gallery\Installer\Schema;

class GallerySettingsFormBlock extends AbstractSettingsFormBlock
{
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var Date
     */
    private $dateHelper;

    /**
     * GallerySettingsFormBlock constructor.
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
            'overlay' => $this->forms->yesNoCheckboxGenerator('overlay', $settings['overlay']),
            'dateformat' => $this->dateHelper->dateFormatDropdown($settings['dateformat']),
            'sidebar_entries' => $this->forms->recordsPerPage((int)$settings['sidebar'], 1, 10, 'sidebar'),
            'form' => \array_merge($settings, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
            'comments' => $this->fetchOptions($settings),
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
