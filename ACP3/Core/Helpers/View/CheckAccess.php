<?php
namespace ACP3\Core\Helpers\View;

use ACP3\Core;

/**
 * Class CheckAccess
 * @package ACP3\Core\Helpers\View
 */
class CheckAccess
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    /**
     * CheckAccess constructor.
     *
     * @param \ACP3\Core\ACL             $acl
     * @param \ACP3\Core\I18n\Translator $translator
     * @param \ACP3\Core\RouterInterface $router
     * @param \ACP3\Core\View            $view
     */
    public function __construct(
        Core\ACL $acl,
        Core\I18n\Translator $translator,
        Core\RouterInterface $router,
        Core\View $view
    ) {
        $this->translator = $translator;
        $this->acl = $acl;
        $this->router = $router;
        $this->view = $view;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function outputLinkOrButton(array $params)
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
                $this->view->assign('access_check', $this->collectData($params, $action, $area));

                return $this->view->fetchTemplate('system/access_check.tpl');
            } elseif ($params['mode'] === 'link' && isset($params['title'])) {
                // If the user has no permission and the type is "link",
                // just return the given title without the surrounding hyperlink
                return $params['title'];
            }
        }

        return '';
    }

    /**
     * @param array  $params
     * @param string $action
     * @param string $area
     *
     * @return array
     */
    private function collectData(array $params, $action, $area)
    {
        $accessCheck = [];
        $accessCheck['uri'] = $this->router->route($params['path']);

        if (isset($params['title'])) {
            $accessCheck['title'] = $params['title'];
        }
        if (isset($params['lang'])) {
            $langArray = explode('|', $params['lang']);
            $accessCheck['lang'] = $this->translator->t($langArray[0], $langArray[1]);
        } else {
            $accessCheck['lang'] = $this->translator->t($action[0], $area . '_' . $action[1] . '_' . $action[2]);
        }
        if (isset($params['class'])) {
            $accessCheck['class'] = $params['class'];
        }

        $accessCheck['mode'] = $params['mode'];

        return $accessCheck;
    }
}
