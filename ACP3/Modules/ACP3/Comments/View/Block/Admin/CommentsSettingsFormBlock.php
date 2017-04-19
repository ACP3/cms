<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\View\Block\Admin;


use ACP3\Core\Helpers\Date;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Comments\Installer\Schema;

class CommentsSettingsFormBlock extends AbstractFormBlock
{
    /**
     * @var SettingsInterface
     */
    private $settings;
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
     * @param FormBlockContext $context
     * @param Modules $modules
     * @param SettingsInterface $settings
     * @param Date $dateHelper
     */
    public function __construct(
        FormBlockContext $context,
        Modules $modules,
        SettingsInterface $settings,
        Date $dateHelper
    ) {
        parent::__construct($context);

        $this->settings = $settings;
        $this->dateHelper = $dateHelper;
        $this->modules = $modules;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        if ($this->modules->isActive('emoticons') === true) {
            $this->view->assign(
                'allow_emoticons',
                $this->forms->yesNoCheckboxGenerator('emoticons', $data['emoticons'])
            );
        }

        return [
            'dateformat' => $this->dateHelper->dateFormatDropdown($data['dateformat']),
            'form_token' => $this->formToken->renderFormToken()
        ];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultData(): array
    {
        return $this->settings->getSettings(Schema::MODULE_NAME);
    }
}
