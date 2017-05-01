<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Extensions;

use ACP3\Core;
use ACP3\Modules\ACP3\System;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Designs extends Core\Controller\AbstractAdminAction
{
    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * Designs constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\BlockInterface $block
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\BlockInterface $block
    ) {
        parent::__construct($context);

        $this->block = $block;
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

        return $this->block->render();
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
            is_file($this->appPath->getDesignRootPathInternal() . $design . '/info.xml') === true
        ) {
            $bool = $this->config->saveSettings(['design' => $design], Schema::MODULE_NAME);

            Core\Cache\Purge::doPurge([
                $this->appPath->getCacheDir() . 'sql',
                $this->appPath->getCacheDir() . 'tpl_compiled',
                $this->appPath->getCacheDir() . 'http'
            ]);
        }

        $text = $this->translator->t('system', $bool === true ? 'designs_edit_success' : 'designs_edit_error');

        return $this->redirectMessages()->setMessage($bool, $text, $this->request->getFullPath());
    }
}
