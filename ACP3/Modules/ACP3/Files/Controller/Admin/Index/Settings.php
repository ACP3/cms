<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Files;

class Settings extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    protected $filesRepository;
    /**
     * @var \ACP3\Modules\ACP3\Files\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Helpers|null
     */
    protected $commentsHelpers;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Core\Helpers\Date
     */
    private $dateHelper;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\Date $dateHelper,
        Files\Validation\AdminSettingsFormValidation $adminSettingsFormValidation,
        ?Comments\Helpers $commentsHelpers = null
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->secureHelper = $secureHelper;
        $this->dateHelper = $dateHelper;
        $this->commentsHelpers = $commentsHelpers;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Files\Installer\Schema::MODULE_NAME);

        if ($this->commentsHelpers) {
            $this->view->assign(
                'comments',
                $this->formsHelper->yesNoCheckboxGenerator('comments', $settings['comments'])
            );
        }

        $orderBy = [
            'date' => $this->translator->t('files', 'order_by_date_descending'),
            'custom' => $this->translator->t('files', 'order_by_custom'),
        ];

        return [
            'order_by' => $this->formsHelper->choicesGenerator('order_by', $orderBy, $settings['order_by']),
            'dateformat' => $this->dateHelper->dateFormatDropdown($settings['dateformat']),
            'sidebar_entries' => $this->formsHelper->recordsPerPage((int) $settings['sidebar'], 1, 10, 'sidebar'),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'dateformat' => $this->secureHelper->strEncode($formData['dateformat']),
                'sidebar' => (int) $formData['sidebar'],
                'order_by' => $formData['order_by'],
            ];

            if ($this->commentsHelpers) {
                $data['comments'] = $formData['comments'];
            }

            return $this->config->saveSettings($data, Files\Installer\Schema::MODULE_NAME);
        });
    }
}
