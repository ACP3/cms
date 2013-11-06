<?php
function smarty_function_check_access($params)
{
    if (isset($params['mode']) && isset($params['path'])) {
        $action = array();
        $query = explode('/', strtolower($params['path']));
        if (isset($query[0]) && $query[0] === 'acp') {
            $action[0] = (isset($query[1]) ? $query[1] : 'acp');
            $action[1] = 'acp_' . (isset($query[2]) ? $query[2] : 'list');
        } else {
            $action[0] = $query[0];
            $action[1] = isset($query[1]) ? $query[1] : 'list';
        }

        if (\ACP3\Core\Modules::hasPermission($action[0], $action[1]) === true) {
            $access_check = array();
            $access_check['uri'] = \ACP3\Core\Registry::get('URI')->route($params['path']);

            if (isset($params['icon'])) {
                $path = ROOT_DIR . CONFIG_ICONS_PATH . $params['icon'] . '.png';
                $access_check['icon'] = $path;
            }
            if (isset($params['title'])) {
                $access_check['title'] = $params['title'];
            }
            if (isset($params['lang'])) {
                $lang_ary = explode('|', $params['lang']);
                $access_check['lang'] = \ACP3\Core\Registry::get('Lang')->t($lang_ary[0], $lang_ary[1]);
            } else {
                $access_check['lang'] = \ACP3\Core\Registry::get('Lang')->t($action[0], $action[1]);
            }

            // Dimensionen der Grafik bestimmen
            if ($params['mode'] === 'link' && isset($params['icon'])) {
                $access_check['width'] = $access_check['height'] = '';

                if (!empty($params['width']) && !empty($params['height']) &&
                    \ACP3\Core\Validate::isNumber($params['width']) === true && \ACP3\Core\Validate::isNumber($params['height']) === true
                ) {
                    $access_check['width'] = ' width="' . $params['width'] . '"';
                    $access_check['height'] = ' height="' . $params['height'] . '"';
                } elseif (is_file(ACP3_ROOT_DIR . $path) === true) {
                    $picInfos = getimagesize(ACP3_ROOT_DIR . $path);
                    $access_check['width'] = ' width="' . $picInfos[0] . '"';
                    $access_check['height'] = ' height="' . $picInfos[1] . '"';
                }
            }

            $access_check['mode'] = $params['mode'];
            \ACP3\Core\Registry::get('View')->assign('access_check', $access_check);
            return \ACP3\Core\Registry::get('View')->fetchTemplate('system/access_check.tpl');
        } elseif ($params['mode'] === 'link' && isset($params['title'])) {
            return $params['title'];
        }
    }
    return '';
}
/* vim: set expandtab: */