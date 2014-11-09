<?php
namespace ACP3\Core\View\Renderer\Smarty\Plugins;

use ACP3\Core;

/**
 * Class CheckAccess
 * @package ACP3\Core\View\Renderer\Smarty
 */
class CheckAccess extends AbstractPlugin
{
    /**
     * @var Core\ACL
     */
    protected $acl;
    /**
     * @var Core\Lang
     */
    protected $lang;
    /**
     * @var Core\Router
     */
    protected $router;
    /**
     * @var Core\View
     */
    protected $view;
    /**
     * @var Icon
     */
    protected $icon;
    /**
     * @var string
     */
    protected $pluginName = 'check_access';

    public function __construct(
        Core\ACL $acl,
        Core\Lang $lang,
        Core\Router $router,
        Core\View $view,
        Icon $icon
    )
    {
        $this->lang = $lang;
        $this->acl = $acl;
        $this->router = $router;
        $this->view = $view;
        $this->icon = $icon;
    }

    /**
     * @param array $params
     * @return mixed|string
     */
    public function process(array $params)
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

            if ($this->acl->hasPermission($permissionPath) === true) {
                $accessCheck = array();
                $accessCheck['uri'] = $this->router->route($params['path']);

                if (isset($params['icon'])) {
                    $iconParams = $this->icon->getImageDimensions(
                        $params['icon'],
                        isset($params['width']) ? $params['width'] : '',
                        isset($params['height']) ? $params['height'] : ''
                    );

                    $accessCheck['icon'] = $iconParams['path'];

                    if ($params['mode'] === 'link') {
                        $accessCheck['width'] = $iconParams['width'];
                        $accessCheck['height'] = $iconParams['height'];
                    }
                }
                if (isset($params['title'])) {
                    $accessCheck['title'] = $params['title'];
                }
                if (isset($params['lang'])) {
                    $langArray = explode('|', $params['lang']);
                    $accessCheck['lang'] = $this->lang->t($langArray[0], $langArray[1]);
                } else {
                    $accessCheck['lang'] = $this->lang->t($action[0], $area . '_' . $action[1] . '_' . $action[2]);
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