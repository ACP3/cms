<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Comments\Controller\Admin\Index
 */
class Settings extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                         $context
     * @param \ACP3\Modules\ACP3\Comments\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     * @param \ACP3\Core\Helpers\FormToken                                       $formTokenHelper
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Comments\Validation\AdminSettingsFormValidation $adminSettingsFormValidation,
        Core\Helpers\FormToken $formTokenHelper)
    {
        parent::__construct($context);

        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->formTokenHelper = $formTokenHelper;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all());
        }

        $settings = $this->config->getSettings('comments');

        // Emoticons erlauben
        if ($this->modules->isActive('emoticons') === true) {
            $this->view->assign('allow_emoticons', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('emoticons', $settings['emoticons']));
        }

        return [
            'dateformat' => $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat']),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData) {
            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'emoticons' => $formData['emoticons'],
            ];

            $this->formTokenHelper->unsetFormToken();

            return $this->config->setSettings($data, 'comments');
        });
    }
}
