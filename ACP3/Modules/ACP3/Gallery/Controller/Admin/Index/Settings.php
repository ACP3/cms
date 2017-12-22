<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Gallery;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Gallery\Controller\Admin\Index
 */
class Settings extends Core\Controller\AbstractFrontendAction
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
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var Core\Cache
     */
    private $galleryCoreCache;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\Cache $galleryCoreCache
     * @param \ACP3\Core\Helpers\Forms $formsHelper
     * @param \ACP3\Core\Helpers\FormToken $formTokenHelper
     * @param \ACP3\Modules\ACP3\Gallery\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Cache $galleryCoreCache,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Gallery\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->galleryCoreCache = $galleryCoreCache;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Gallery\Installer\Schema::MODULE_NAME);

        if ($this->modules->isActive('comments') === true) {
            $this->view->assign('comments',
                $this->formsHelper->yesNoCheckboxGenerator('comments', $settings['comments'])
            );
        }

        return [
            'overlay' => $this->formsHelper->yesNoCheckboxGenerator('overlay', $settings['overlay']),
            'dateformat' => $this->get('core.helpers.date')->dateFormatDropdown($settings['dateformat']),
            'sidebar_entries' => $this->formsHelper->recordsPerPage((int)$settings['sidebar'], 1, 10, 'sidebar'),
            'form' => array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken()
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'width' => (int)$formData['width'],
                'height' => (int)$formData['height'],
                'thumbwidth' => (int)$formData['thumbwidth'],
                'thumbheight' => (int)$formData['thumbheight'],
                'overlay' => $formData['overlay'],
                'dateformat' => $this->get('core.helpers.secure')->strEncode($formData['dateformat']),
                'sidebar' => (int)$formData['sidebar'],
            ];
            if ($this->modules->isActive('comments') === true) {
                $data['comments'] = (int)$formData['comments'];
            }

            $bool = $this->config->saveSettings($data, Gallery\Installer\Schema::MODULE_NAME);

            if ($this->hasImageDimensionChanges($formData)) {
                Core\Cache\Purge::doPurge($this->appPath->getUploadsDir() . 'gallery/cache', 'gallery');

                $this->galleryCoreCache->getDriver()->deleteAll();
            }

            return $bool;
        });
    }

    /**
     * @param array $formData
     * @return bool
     */
    protected function hasImageDimensionChanges(array $formData)
    {
        $settings = $this->config->getSettings(Gallery\Installer\Schema::MODULE_NAME);

        return $formData['thumbwidth'] !== $settings['thumbwidth']
            || $formData['thumbheight'] !== $settings['thumbheight']
            || $formData['width'] !== $settings['width']
            || $formData['height'] !== $settings['height'];
    }
}
