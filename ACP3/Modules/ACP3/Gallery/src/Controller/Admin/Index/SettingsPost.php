<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Gallery;

class SettingsPost extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Gallery\Validation\AdminSettingsFormValidation
     */
    private $adminSettingsFormValidation;
    /**
     * @var Core\Cache
     */
    private $galleryCoreCache;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Action $actionHelper,
        Core\Cache $galleryCoreCache,
        Core\Helpers\Secure $secureHelper,
        Gallery\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->galleryCoreCache = $galleryCoreCache;
        $this->secureHelper = $secureHelper;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'width' => (int) $formData['width'],
                'height' => (int) $formData['height'],
                'thumbwidth' => (int) $formData['thumbwidth'],
                'thumbheight' => (int) $formData['thumbheight'],
                'overlay' => $formData['overlay'],
                'dateformat' => $this->secureHelper->strEncode($formData['dateformat']),
                'sidebar' => (int) $formData['sidebar'],
            ];

            $bool = $this->config->saveSettings($data, Gallery\Installer\Schema::MODULE_NAME);

            if ($this->hasImageDimensionChanges($formData)) {
                Core\Cache\Purge::doPurge($this->appPath->getUploadsDir() . 'gallery/cache', 'gallery');

                $this->galleryCoreCache->deleteAll();
            }

            return $bool;
        });
    }

    private function hasImageDimensionChanges(array $formData): bool
    {
        $settings = $this->config->getSettings(Gallery\Installer\Schema::MODULE_NAME);

        return $formData['thumbwidth'] !== $settings['thumbwidth']
            || $formData['thumbheight'] !== $settings['thumbheight']
            || $formData['width'] !== $settings['width']
            || $formData['height'] !== $settings['height'];
    }
}
