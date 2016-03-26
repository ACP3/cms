<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Maintenance;

use ACP3\Core;
use ACP3\Modules\ACP3\System;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\System\Controller\Admin\Maintenance
 */
class Cache extends Core\Controller\AdminAction
{
    /**
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute($action = '')
    {
        if (!empty($action)) {
            list($result, $text) = $this->executePurge($action);

            return $this->redirectMessages()->setMessage($result, $text, 'acp/system/maintenance/cache');
        }
    }

    /**
     * @param $action
     *
     * @return array
     */
    protected function executePurge($action)
    {
        $cacheTypes = [
            'general' => $this->appPath->getCacheDir() . 'sql',
            'images' => $this->appPath->getCacheDir() . 'images',
            'minify' => $this->appPath->getUploadsDir() . 'assets',
            'templates' => [
                $this->appPath->getCacheDir() . 'tpl_compiled',
                $this->appPath->getCacheDir() . 'tpl_cached'
            ]
        ];

        $result = false;
        switch ($action) {
            case 'general':
            case 'images':
            case 'minify':
            case 'templates':
                $result = Core\Cache\Purge::doPurge($cacheTypes[$action]);
                $text = $this->translator->t(
                    'system',
                    $result === true
                        ? 'cache_type_' . $action . '_delete_success'
                        : 'cache_type_' . $action . '_delete_error'
                );
                break;
            default:
                $text = $this->translator->t('system', 'cache_type_not_found');
        }
        return [$result, $text];
    }
}
