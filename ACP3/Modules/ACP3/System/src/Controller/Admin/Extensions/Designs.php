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
    public function __construct(
        WidgetContext $context,
        private AdminThemesViewProvider $adminThemesViewProvider,
        private ThemePathInterface $theme,
        private RedirectMessages $redirectMessages,
        private CacheClearService $cacheClearService
    ) {
        parent::__construct($context);
    }

    public function __invoke(?string $dir = null): array|\Symfony\Component\HttpFoundation\RedirectResponse
    {
        if (!empty($dir)) {
            return $this->doUpdateTheme($dir);
        }

        return ($this->adminThemesViewProvider)();
    }

    private function doUpdateTheme(string $design): \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
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
