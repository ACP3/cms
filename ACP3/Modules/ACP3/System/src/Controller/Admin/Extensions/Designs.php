<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Extensions;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Environment\ThemePathInterface;
use ACP3\Core\Helpers\RedirectMessages;
use ACP3\Modules\ACP3\System\Installer\Schema;
use ACP3\Modules\ACP3\System\Services\CacheClearService;
use ACP3\Modules\ACP3\System\ViewProviders\AdminThemesViewProvider;

class Designs extends AbstractWidgetAction
{
    /**
     * @var \ACP3\Core\Helpers\RedirectMessages
     */
    private $redirectMessages;
    /**
     * @var \ACP3\Modules\ACP3\System\Services\CacheClearService
     */
    private $cacheClearService;
    /**
     * @var \ACP3\Core\Environment\ThemePathInterface
     */
    private $theme;
    /**
     * @var \ACP3\Modules\ACP3\System\ViewProviders\AdminThemesViewProvider
     */
    private $adminThemesViewProvider;

    public function __construct(
        WidgetContext $context,
        AdminThemesViewProvider $adminThemesViewProvider,
        ThemePathInterface $theme,
        RedirectMessages $redirectMessages,
        CacheClearService $cacheClearService
    ) {
        parent::__construct($context);

        $this->redirectMessages = $redirectMessages;
        $this->cacheClearService = $cacheClearService;
        $this->theme = $theme;
        $this->adminThemesViewProvider = $adminThemesViewProvider;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function __invoke(?string $dir = null)
    {
        if (!empty($dir)) {
            return $this->doUpdateTheme($dir);
        }

        return ($this->adminThemesViewProvider)();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function doUpdateTheme(string $design)
    {
        $result = false;

        if ($this->theme->has($design)) {
            $result = $this->config->saveSettings(['design' => $design], Schema::MODULE_NAME);

            $this->cacheClearService->clearCacheByType('templates');
            $this->cacheClearService->clearCacheByType('general');
        }

        $text = $this->translator->t('system', $result === true ? 'designs_edit_success' : 'designs_edit_error');

        return $this->redirectMessages->setMessage($result, $text, $this->request->getFullPath());
    }
}
