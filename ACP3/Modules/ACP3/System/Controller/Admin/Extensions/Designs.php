<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Extensions;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;
use ACP3\Modules\ACP3\System;

/**
 * Class Designs
 * @package ACP3\Modules\ACP3\System\Controller\Admin\Extensions
 */
class Designs extends Core\Controller\AdminAction
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

        $designs = [];
        $path = ACP3_ROOT_DIR . 'designs/';
        $directories = Core\Filesystem::scandir($path);
        $countDir = count($directories);
        for ($i = 0; $i < $countDir; ++$i) {
            $designInfo = $this->xml->parseXmlFile($path . $directories[$i] . '/info.xml', '/design');
            if (!empty($designInfo)) {
                $designs[$i] = $designInfo;
                $designs[$i]['selected'] = $this->config->getSettings('system')['design'] === $directories[$i] ? 1 : 0;
                $designs[$i]['dir'] = $directories[$i];
            }
        }

        return [
            'designs' => $designs
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
            $bool = $this->config->setSettings(['design' => $design], 'system');

            // Template Cache leeren
            Core\Cache\Purge::doPurge([
                $this->appPath->getCacheDir() . 'tpl_compiled',
                $this->appPath->getCacheDir() . 'tpl_cached'
            ]);
        }

        $text = $this->translator->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

        return $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }
}
