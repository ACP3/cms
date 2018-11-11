<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Extensions;

use ACP3\Core;
use ACP3\Modules\ACP3\System;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Designs extends Core\Controller\AbstractFormAction
{
    use System\Helper\AvailableDesignsTrait;

    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\XML $xml
    ) {
        parent::__construct($context);

        $this->xml = $xml;
    }

    /**
     * @param string $dir
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(string $dir = '')
    {
        if (!empty($dir)) {
            return $this->executePost($dir);
        }

        return [
            'designs' => $this->getAvailableDesigns(),
        ];
    }

    /**
     * @param string $design
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost(string $design)
    {
        $bool = false;

        if ((bool) \preg_match('=/=', $design) === false &&
            \is_file($this->appPath->getDesignRootPathInternal() . $design . '/info.xml') === true
        ) {
            $bool = $this->config->saveSettings(['design' => $design], Schema::MODULE_NAME);

            Core\Cache\Purge::doPurge([
                $this->appPath->getCacheDir() . 'sql',
                $this->appPath->getCacheDir() . 'tpl_compiled',
                $this->appPath->getCacheDir() . 'http',
            ]);
        }

        $text = $this->translator->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

        return $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }

    /**
     * {@inheritdoc}
     */
    protected function getXml()
    {
        return $this->xml;
    }

    /**
     * {@inheritdoc}
     */
    protected function selectEntry($directory)
    {
        return $this->config->getSettings(Schema::MODULE_NAME)['design'] === $directory ? 1 : 0;
    }
}
