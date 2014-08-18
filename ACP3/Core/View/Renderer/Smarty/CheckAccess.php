<?php
namespace ACP3\Core\View\Renderer\Smarty;

use ACP3\Core;

/**
 * Class CheckAccess
 * @package ACP3\Core\View\Renderer\Smarty
 */
class CheckAccess extends AbstractPlugin
{
    /**
     * @var Core\Lang
     */
    protected $lang;
    /**
     * @var Core\Modules
     */
    protected $modules;
    /**
     * @var Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\Validator\Rules\Misc
     */
    protected $validate;
    /**
     * @var Core\View
     */
    protected $view;
    /**
     * @var string
     */
    protected $pluginName = 'check_access';

    public function __construct(
        Core\Lang $lang,
        Core\Modules $modules,
        Core\Router $router,
        Core\Validator\Rules\Misc $validate,
        Core\View $view
    )
    {
        $this->lang = $lang;
        $this->modules = $modules;
        $this->router = $router;
        $this->validate = $validate;
        $this->view = $view;
    }

    /**
     * @param $params
     *
     * @throws \Exception
     * @return string
     */
    public function process($params)
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

            if ($this->modules->hasPermission($permissionPath) === true) {
                $accessCheck = array();
                $accessCheck['uri'] = $this->router->route($params['path']);

                $path = '';
                if (isset($params['icon'])) {
                    $path = ROOT_DIR . CONFIG_ICONS_PATH . $params['icon'] . '.png';
                    $accessCheck['icon'] = $path;
                }
                if (isset($params['title'])) {
                    $accessCheck['title'] = $params['title'];
                }
                if (isset($params['lang'])) {
                    $lang_ary = explode('|', $params['lang']);
                    $accessCheck['lang'] = $this->lang->t($lang_ary[0], $lang_ary[1]);
                } else {
                    $accessCheck['lang'] = $this->lang->t($action[0], $area . '_' . $action[1] . '_' . $action[2]);
                }

                // Dimensionen der Grafik bestimmen
                if ($params['mode'] === 'link' && isset($params['icon'])) {
                    $accessCheck['width'] = $accessCheck['height'] = '';

                    if (!empty($params['width']) && !empty($params['height']) &&
                        $this->validate->isNumber($params['width']) === true && $this->validate->isNumber($params['height']) === true
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
                $this->view->assign('access_check', $accessCheck);
                return $this->view->fetchTemplate('system/access_check.tpl');
            } elseif ($params['mode'] === 'link' && isset($params['title'])) {
                return $params['title'];
            }
        }

        return '';
    }
}