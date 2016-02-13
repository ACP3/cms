<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Files;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Files\Controller\Admin\Index
 */
class Settings extends Core\Modules\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\FilesRepository
     */
    protected $filesRepository;
    /**
     * @var \ACP3\Modules\ACP3\Files\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Comments\Helpers
     */
    protected $commentsHelpers;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext                      $context
     * @param \ACP3\Core\Helpers\FormToken                                    $formTokenHelper
     * @param \ACP3\Modules\ACP3\Files\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Files\Validation\AdminSettingsFormValidation $adminSettingsFormValidation)
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
    }

    /**
     * @param \ACP3\Modules\ACP3\Comments\Helpers $commentsHelpers
     *
     * @return $this
     */
    public function setCommentsHelpers(Comments\Helpers $commentsHelpers)
    {
        $this->commentsHelpers = $commentsHelpers;

        return $this;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->settingsPost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('files');

        if ($this->commentsHelpers) {
            $this->view->assign('comments', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('comments', $settings['comments']));
        }

        $this->formTokenHelper->generateFormToken();

        return [
            'dateformat' => $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat']),
            'sidebar_entries' => $this->get('core.helpers.forms')->recordsPerPage((int)$settings['sidebar'], 1, 10)
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function settingsPost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'sidebar' => (int)$formData['sidebar']
            ];

            if ($this->commentsHelpers) {
                $data['comments'] = $formData['comments'];
            }

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'files');
        });
    }
}
