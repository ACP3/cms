<?php
function smarty_function_check_access($params)
{
    if (isset($params['mode']) && isset($params['path'])) {
        $action = array();
        $query = explode('/', strtolower($params['path']));

        if (isset($query[0]) && $query[0] === 'acp') {
            $action[0] = (isset($query[1]) ? $query[1] : 'acp');
            $action[1] = (isset($query[2]) ? $query[2] : 'index');
            $action[2] = isset($query[3]) ? $query[3] : 'index';

            $area = 'admin';
        } else {
            $action[0] = $query[0];
            $action[1] = isset($query[1]) ? $query[1] : 'index';
            $action[2] = isset($query[2]) ? $query[2] : 'index';

            $area = 'frontend';
        }

        $permissionPath = $area . '/' . $action[0] . '/' . $action[1] . '/' . $action[2];

        if (\ACP3\Core\Modules::hasPermission($permissionPath) === true) {
            $accessCheck = array();
            $accessCheck['uri'] = \ACP3\Core\Registry::get('URI')->route($params['path']);

            if (isset($params['icon'])) {
                $path = ROOT_DIR . CONFIG_ICONS_PATH . $params['icon'] . '.png';
                $accessCheck['icon'] = $path;
            }
            if (isset($params['title'])) {
                $accessCheck['title'] = $params['title'];
            }
            if (isset($params['lang'])) {
                $lang_ary = explode('|', $params['lang']);
                $accessCheck['lang'] = \ACP3\Core\Registry::get('Lang')->t($lang_ary[0], $lang_ary[1]);
            } else {
                $accessCheck['lang'] = \ACP3\Core\Registry::get('Lang')->t($action[0], $action[2]);
            }

            // Dimensionen der Grafik bestimmen
            if ($params['mode'] === 'link' && isset($params['icon'])) {
                $accessCheck['width'] = $accessCheck['height'] = '';

                if (!empty($params['width']) && !empty($params['height']) &&
                    \ACP3\Core\Validate::isNumber($params['width']) === true && \ACP3\Core\Validate::isNumber($params['height']) === true
                ) {
                    $accessCheck['width'] = ' width="' . $params['width'] . '"';
                    $accessCheck['height'] = ' height="' . $params['height'] . '"';
                } elseif (is_file(ACP3_ROOT_DIR . $path) === true) {
                    $picInfos = getimagesize(ACP3_ROOT_DIR . $path);
                    $accessCheck['width'] = ' width="' . $picInfos[0] . '"';
                    $accessCheck['height'] = ' height="' . $picInfos[1] . '"';
                }
            }

            $accessCheck['mode'] = $params['mode'];
            \ACP3\Core\Registry::get('View')->assign('access_check', $accessCheck);
            return \ACP3\Core\Registry::get('View')->fetchTemplate('system/access_check.tpl');
        } elseif ($params['mode'] === 'link' && isset($params['title'])) {
            return $params['title'];
        }
    }
    return '';
}
/* vim: set expandtab: */