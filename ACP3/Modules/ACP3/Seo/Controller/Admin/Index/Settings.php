<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Index
 * @package ACP3\Modules\ACP3\Seo\Controller\Admin\Index
 */
class Settings extends Core\Modules\AdminController
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
     * Settings constructor.
     *
     * @param \ACP3\Core\Modules\Controller\AdminContext                    $context
     * @param \ACP3\Core\Helpers\FormToken                                  $formTokenHelper
     * @param \ACP3\Modules\ACP3\Seo\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Modules\Controller\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Seo\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

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
        $lang_robots = [
            $this->translator->t('seo', 'robots_index_follow'),
            $this->translator->t('seo', 'robots_index_nofollow'),
            $this->translator->t('seo', 'robots_noindex_follow'),
            $this->translator->t('seo', 'robots_noindex_nofollow')
        ];

        $this->formTokenHelper->generateFormToken();

        return [
            'robots' => $this->get('core.helpers.forms')->selectGenerator('robots', [1, 2, 3, 4], $lang_robots,
                $seoSettings['robots']),
            'mod_rewrite' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('mod_rewrite',
                $seoSettings['mod_rewrite']),
            'form' => array_merge($seoSettings, $this->request->getPost()->all())
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

            // Config aktualisieren
            $data = [
                'meta_description' => Core\Functions::strEncode($formData['meta_description']),
                'meta_keywords' => Core\Functions::strEncode($formData['meta_keywords']),
                'mod_rewrite' => (int)$formData['mod_rewrite'],
                'robots' => (int)$formData['robots'],
                'title' => Core\Functions::strEncode($formData['title']),
            ];

            $bool = $this->config->setSettings($data, 'seo');

            $this->formTokenHelper->unsetFormToken();

            return $bool;
        });
    }
}