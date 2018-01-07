<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\View\Block\Admin;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractSettingsFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Comments\Installer\Schema;

class CommentsSettingsFormBlock extends AbstractSettingsFormBlock
{
    /**
     * @var Date
     */
    private $dateHelper;
    /**
     * @var Modules
     */
    private $modules;

    /**
     * CommentsSettingsFormBlock constructor.
     *
     * @param FormBlockContext  $context
     * @param Modules           $modules
     * @param SettingsInterface $settings
     * @param Date              $dateHelper
     */
    public function __construct(
        FormBlockContext $context,
        Modules $modules,
        SettingsInterface $settings,
        Date $dateHelper
    ) {
        parent::__construct($context, $settings);

        $this->dateHelper = $dateHelper;
        $this->modules = $modules;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $data = $this->getData();

        return [
            'dateformat' => $this->dateHelper->dateFormatDropdown($data['dateformat']),
            'form_token' => $this->formToken->renderFormToken(),
            'allow_emoticons' => $this->fetchAllowEmoticons($data['emoticons']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }

    /**
     * @param int $allowEmoticons
     *
     * @return array
     */
    private function fetchAllowEmoticons(int $allowEmoticons): array
    {
        if ($this->modules->isActive('emoticons') === true) {
            return $this->forms->yesNoCheckboxGenerator('emoticons', $allowEmoticons);
        }

        return [];
    }
}
