<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Index
 */
class Settings extends Core\Controller\AdminController
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext                        $context
     * @param \ACP3\Core\Helpers\FormToken                                      $formTokenHelper
     * @param \ACP3\Modules\ACP3\Gallery\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        Gallery\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    )
    {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute()
    {
        $settings = $this->config->getSettings('gallery');

        if ($this->request->getPost()->isEmpty() === false) {
            return $this->executePost($this->request->getPost()->all(), $settings);
        }

        if ($this->modules->isActive('comments') === true) {
            $this->view->assign('comments', $this->get('core.helpers.forms')->yesNoCheckboxGenerator('comments', $settings['comments']));
        }

        return [
            'overlay' => $this->get('core.helpers.forms')->yesNoCheckboxGenerator('overlay', $settings['overlay']),
            'dateformat' => $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat']),
            'sidebar_entries' => $this->get('core.helpers.forms')->recordsPerPage((int)$settings['sidebar'], 1, 10),
            'form' => array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @param array $formData
     * @param array $settings
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(array $formData, array $settings)
    {
        return $this->actionHelper->handleSettingsPostAction(function () use ($formData, $settings) {
            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'width' => (int)$formData['width'],
                'height' => (int)$formData['height'],
                'thumbwidth' => (int)$formData['thumbwidth'],
                'thumbheight' => (int)$formData['thumbheight'],
                'overlay' => $formData['overlay'],
                'dateformat' => Core\Functions::strEncode($formData['dateformat']),
                'sidebar' => (int)$formData['sidebar'],
            ];
            if ($this->modules->isActive('comments') === true) {
                $data['comments'] = (int)$formData['comments'];
            }

            $this->formTokenHelper->unsetFormToken();

            $bool = $this->config->setSettings($data, 'gallery');

            // Falls sich die anzuzeigenden Bildgrößen geändert haben, die gecacheten Bilder löschen
            if ($formData['thumbwidth'] !== $settings['thumbwidth'] || $formData['thumbheight'] !== $settings['thumbheight'] ||
                $formData['width'] !== $settings['width'] || $formData['height'] !== $settings['height']
            ) {
                Core\Cache::purge($this->appPath->getCacheDir() . 'images', 'gallery');

                $this->get('gallery.cache.core')->getDriver()->deleteAll();
            }

            return $bool;
        });
    }
}
