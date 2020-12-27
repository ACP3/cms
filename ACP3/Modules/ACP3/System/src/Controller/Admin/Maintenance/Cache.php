<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Maintenance;

use ACP3\Core;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Helpers\RedirectMessages;
use ACP3\Modules\ACP3\System;

class Cache extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\RedirectMessages
     */
    private $redirectMessages;

    public function __construct(
        FrontendContext $context,
        RedirectMessages $redirectMessages
    ) {
        parent::__construct($context);

        $this->redirectMessages = $redirectMessages;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute(?string $action = null)
    {
        if ($action !== null) {
            return $this->executePurge($action);
        }

        return [
            'cache_types' => [
                'general',
                'minify',
                'page',
                'templates',
            ],
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function executePurge(string $action)
    {
        $cacheTypes = [
            'general' => [
                $this->appPath->getCacheDir() . 'http',
                $this->appPath->getCacheDir() . 'sql',
            ],
            'minify' => $this->appPath->getUploadsDir() . 'assets',
            'page' => $this->appPath->getCacheDir() . 'http',
            'templates' => [
                $this->appPath->getCacheDir() . 'tpl_compiled',
            ],
        ];

        $result = false;
        switch ($action) {
            case 'general':
            case 'minify':
            case 'page':
            case 'templates':
                $result = Core\Cache\Purge::doPurge($cacheTypes[$action]);
                $text = $this->translator->t(
                    'system',
                    $result === true
                        ? 'cache_type_' . $action . '_delete_success'
                        : 'cache_type_' . $action . '_delete_error'
                );

                if ($action === 'page') {
                    $this->config->saveSettings(
                        ['page_cache_is_valid' => true],
                        System\Installer\Schema::MODULE_NAME
                    );
                }

                break;
            default:
                $text = $this->translator->t('system', 'cache_type_not_found');
        }

        return $this->redirectMessages->setMessage($result, $text, 'acp/system/maintenance/cache');
    }
}
