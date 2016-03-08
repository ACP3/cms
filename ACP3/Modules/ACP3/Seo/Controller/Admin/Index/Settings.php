<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Seo\Controller\Admin\Index
 */
class Settings extends Core\Controller\AdminAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                    $context
     * @param \ACP3\Core\Helpers\Forms                                      $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                  $formTokenHelper
     * @param \ACP3\Modules\ACP3\Seo\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Seo\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all());
        }

        $seoSettings = $this->config->getSettings('seo');

        // Robots
        $robots = [
            1 => $this->translator->t('seo', 'robots_index_follow'),
            2 => $this->translator->t('seo', 'robots_index_nofollow'),
            3 => $this->translator->t('seo', 'robots_noindex_follow'),
            4 => $this->translator->t('seo', 'robots_noindex_nofollow')
        ];

        return [
            'robots' => $this->formsHelper->choicesGenerator('robots', $robots, $seoSettings['robots']),
            'mod_rewrite' => $this->formsHelper->yesNoCheckboxGenerator('mod_rewrite', $seoSettings['mod_rewrite']),
            'form' => array_merge($seoSettings, $this->request->getPost()->all()),
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
                'meta_description' => $this->get('core.helpers.secure')->strEncode($formData['meta_description']),
                'meta_keywords' => $this->get('core.helpers.secure')->strEncode($formData['meta_keywords']),
                'mod_rewrite' => (int)$formData['mod_rewrite'],
                'robots' => (int)$formData['robots'],
                'title' => $this->get('core.helpers.secure')->strEncode($formData['title']),
            ];

            $bool = $this->config->setSettings($data, 'seo');

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}
