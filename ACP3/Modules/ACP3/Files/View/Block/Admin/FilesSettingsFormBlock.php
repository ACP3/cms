<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\View\Block\Admin;


use ACP3\Core\Helpers\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Comments\Helpers;
use ACP3\Modules\ACP3\Files\Installer\Schema;

class FilesSettingsFormBlock extends AbstractFormBlock
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
     * @var Helpers|null
     */
    private $commentsHelpers;

    /**
     * FilesSettingsFormBlock constructor.
     * @param FormBlockContext $context
     * @param Date $dateHelper
     * @param SettingsInterface $settings
     */
    public function __construct(FormBlockContext $context, Date $dateHelper, SettingsInterface $settings)
    {
        parent::__construct($context);

        $this->settings = $settings;
        $this->dateHelper = $dateHelper;
    }

    /**
     * @param Helpers|null $commentsHelpers
     * @return $this
     */
    public function setCommentsHelpers(Helpers $commentsHelpers)
    {
        $this->commentsHelpers = $commentsHelpers;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        if ($this->commentsHelpers) {
            $this->view->assign(
                'comments',
                $this->forms->yesNoCheckboxGenerator('comments', $data['comments'])
            );
        }

        $orderBy = [
            'date' => $this->translator->t('files', 'order_by_date_descending'),
            'custom' => $this->translator->t('files', 'order_by_custom')
        ];

        return [
            'order_by' => $this->forms->choicesGenerator('order_by', $orderBy, $data['order_by']),
            'dateformat' => $this->dateHelper->dateFormatDropdown($data['dateformat']),
            'sidebar_entries' => $this->forms->recordsPerPage((int)$data['sidebar'], 1, 10, 'sidebar'),
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
