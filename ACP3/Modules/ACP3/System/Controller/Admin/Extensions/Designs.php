<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Extensions;

use ACP3\Core;
use ACP3\Modules\ACP3\System;
use ACP3\Modules\ACP3\System\Installer\Schema;

/**
 * Class Designs
 * @package ACP3\Modules\ACP3\System\Controller\Admin\Extensions
 */
class Designs extends Core\Controller\AbstractAdminAction
{
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;

    /**
     * Designs constructor.
     *
     * @param \ACP3\Core\Controller\Context\AdminContext $context
     * @param \ACP3\Core\XML                             $xml
     */
    public function __construct(
        Core\Controller\Context\AdminContext $context,
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
    public function execute($dir = '')
    {
        if (!empty($dir)) {
            return $this->executePost($dir);
        }

        return [
            'designs' => $this->getAvailableDesigns()
        ];
    }

    /**
     * @param string $design
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executePost($design)
    {
        $bool = false;

        if ((bool)preg_match('=/=', $design) === false &&
            is_file(ACP3_ROOT_DIR . 'designs/' . $design . '/info.xml') === true
        ) {
            $bool = $this->config->setSettings(['design' => $design], Schema::MODULE_NAME);

            // Template Cache leeren
            Core\Cache\Purge::doPurge([
                $this->appPath->getCacheDir() . 'tpl_compiled',
                $this->appPath->getCacheDir() . 'tpl_cached',
                $this->appPath->getCacheDir() . 'http'
            ]);
        }

        $text = $this->translator->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

        return $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }

    /**
     * @return array
     */
    protected function getAvailableDesigns()
    {
        $designs = [];
        $path = ACP3_ROOT_DIR . 'designs/';
        $directories = Core\Filesystem::scandir($path);
        foreach ($directories as $directory) {
            $designInfo = $this->xml->parseXmlFile($path . $directory . '/info.xml', '/design');
            if (!empty($designInfo)) {
                $designs[] = array_merge(
                    $designInfo,
                    [
                        'selected' => $this->config->getSettings(Schema::MODULE_NAME)['design'] === $directory ? 1 : 0,
                        'dir' => $directory
                    ]
                );
            }
        }

        return $designs;
    }
}
