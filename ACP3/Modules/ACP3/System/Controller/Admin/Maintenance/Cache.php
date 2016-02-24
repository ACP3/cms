<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Maintenance;

use ACP3\Core;
use ACP3\Modules\ACP3\System;

/**
 * Class Cache
 * @package ACP3\Modules\ACP3\System\Controller\Admin\Maintenance
 */
class Cache extends Core\Controller\AdminController
{
    /**
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute($action = '')
    {
        if (!empty($action)) {
            $result = false;
            switch ($action) {
                case 'general':
                    $result = Core\Cache::purge($this->appPath->getCacheDir() . 'sql');
                    $text = $this->translator->t('system',
                        $result === true ? 'cache_type_general_delete_success' : 'cache_type_general_delete_success');
                    break;
                case 'images':
                    $result = Core\Cache::purge($this->appPath->getCacheDir() . 'images');
                    $text = $this->translator->t('system',
                        $result === true ? 'cache_type_images_delete_success' : 'cache_type_images_delete_success');
                    break;
                case 'minify':
                    $result = Core\Cache::purge($this->appPath->getUploadsDir() . 'assets');
                    $text = $this->translator->t('system',
                        $result === true ? 'cache_type_minify_delete_success' : 'cache_type_minify_delete_success');
                    break;
                case 'templates':
                    $result = (Core\Cache::purge($this->appPath->getCacheDir() . 'tpl_compiled') && Core\Cache::purge($this->appPath->getCacheDir() . 'tpl_cached'));
                    $text = $this->translator->t('system',
                        $result === true ? 'cache_type_templates_delete_success' : 'cache_type_templates_delete_success');
                    break;
                default:
                    $text = $this->translator->t('system', 'cache_type_not_found');
            }

            return $this->redirectMessages()->setMessage($result, $text, 'acp/system/maintenance/cache');
        }
    }
}
