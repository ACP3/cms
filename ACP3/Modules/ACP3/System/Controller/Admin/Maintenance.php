<?php

namespace ACP3\Modules\ACP3\System\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\ACP3\System;

/**
 * Class Maintenance
 * @package ACP3\Modules\ACP3\System\Controller\Admin
 */
class Maintenance extends Core\Modules\AdminController
{
    /**
     * @param string $action
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function actionCache($action = '')
    {
        if (!empty($action)) {
            $result = false;
            switch ($action) {
                case 'general':
                    $result = Core\Cache::purge(CACHE_DIR . 'sql');
                    $text = $this->lang->t('system', $result === true ? 'cache_type_general_delete_success' : 'cache_type_general_delete_success');
                    break;
                case 'images':
                    $result = Core\Cache::purge(CACHE_DIR . 'images');
                    $text = $this->lang->t('system', $result === true ? 'cache_type_images_delete_success' : 'cache_type_images_delete_success');
                    break;
                case 'minify':
                    $result = Core\Cache::purge(UPLOADS_DIR . 'assets');
                    $text = $this->lang->t('system', $result === true ? 'cache_type_minify_delete_success' : 'cache_type_minify_delete_success');
                    break;
                case 'templates':
                    $result = (Core\Cache::purge(CACHE_DIR . 'tpl_compiled') && Core\Cache::purge(CACHE_DIR . 'tpl_cached'));
                    $text = $this->lang->t('system', $result === true ? 'cache_type_templates_delete_success' : 'cache_type_templates_delete_success');
                    break;
                default:
                    $text = $this->lang->t('system', 'cache_type_not_found');
            }

            return $this->redirectMessages()->setMessage($result, $text, 'acp/system/maintenance/cache');
        }
    }

    public function actionIndex()
    {
        return;
    }

    public function actionUpdateCheck()
    {
        $file = @file_get_contents('http://www.acp3-cms.net/update.txt');
        if ($file !== false) {
            $data = explode('||', $file);
            if (count($data) === 2) {
                $update = [
                    'installed_version' => Core\Application\BootstrapInterface::VERSION,
                    'current_version' => $data[0],
                ];

                if (version_compare($update['installed_version'], $update['current_version'], '>=')) {
                    $update['text'] = $this->lang->t('system', 'acp3_up_to_date');
                    $update['class'] = 'success';
                } else {
                    $update['text'] = $this->lang->t(
                        'system',
                        'acp3_not_up_to_date',
                        [
                            '%link_start%' => '<a href="' . $data[1] . '" target="_blank">',
                            '%link_end%' => '</a>'
                        ]
                    );
                    $update['class'] = 'error';
                }

                $this->view->assign('update', $update);
            }
        }
    }
}
