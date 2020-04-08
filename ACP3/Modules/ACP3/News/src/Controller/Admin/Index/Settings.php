<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\News;

class Settings extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\News\Validation\AdminSettingsFormValidation
     */
    private $adminSettingsFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Core\Helpers\Date
     */
    private $dateHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secureHelper,
        Core\Helpers\Date $dateHelper,
        News\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->secureHelper = $secureHelper;
        $this->dateHelper = $dateHelper;
    }

    public function execute(): array
    {
        $settings = $this->config->getSettings(News\Installer\Schema::MODULE_NAME);

        return [
            'dateformat' => $this->dateHelper->dateFormatDropdown($settings['dateformat']),
            'readmore' => $this->formsHelper->yesNoCheckboxGenerator('readmore', $settings['readmore']),
            'readmore_chars' => $this->request->getPost()->get('readmore_chars', $settings['readmore_chars']),
            'sidebar_entries' => $this->formsHelper->recordsPerPage((int) $settings['sidebar'], 1, 10, 'sidebar'),
            'category_in_breadcrumb' => $this->formsHelper->yesNoCheckboxGenerator(
                'category_in_breadcrumb',
                $settings['category_in_breadcrumb']
            ),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'dateformat' => $this->secureHelper->strEncode($formData['dateformat']),
                'sidebar' => (int) $formData['sidebar'],
                'readmore' => $formData['readmore'],
                'readmore_chars' => (int) $formData['readmore_chars'],
                'category_in_breadcrumb' => $formData['category_in_breadcrumb'],
            ];

            return $this->config->saveSettings($data, News\Installer\Schema::MODULE_NAME);
        });
    }
}
