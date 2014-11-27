<?php
namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

/**
 * Class CheckAccess
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class CheckAccess extends AbstractFunction
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
     * @var Icon
     */
    protected $icon;

    /**
     * @param Core\ACL $acl
     * @param Core\Lang $lang
     * @param Core\Router $router
     * @param Icon $icon
     */
    public function __construct(
        Core\ACL $acl,
        Core\Lang $lang,
        Core\Router $router,
        Icon $icon
    ) {
        $this->lang = $lang;
        $this->acl = $acl;
        $this->router = $router;
        $this->icon = $icon;
    }

    /**
     * @inheritdoc
     */
    public function getPluginName()
    {
        return 'check_access';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        if (isset($params['mode']) && isset($params['path'])) {
            $action = [];
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
                $accessCheck = [];
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
                $smarty->assign('access_check', $accessCheck);
                return $smarty->fetch('asset:system/access_check.tpl');
            } elseif ($params['mode'] === 'link' && isset($params['title'])) {
                return $params['title'];
            }
        }

        return '';
    }
}
