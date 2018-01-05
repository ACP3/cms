<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\View;

use ACP3\Core;

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
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;

    /**
     * CheckAccess constructor.
     *
     * @param \ACP3\Core\ACL             $acl
     * @param \ACP3\Core\I18n\Translator $translator
     * @param \ACP3\Core\Router\RouterInterface $router
     */
    public function __construct(
        Core\ACL $acl,
        Core\I18n\Translator $translator,
        Core\Router\RouterInterface $router
    ) {
        $this->translator = $translator;
        $this->acl = $acl;
        $this->router = $router;
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
            $query = \explode('/', \strtolower($params['path']));

            if (isset($query[0]) && $query[0] === 'acp') {
                $action[0] = ($query[1] ?? 'acp');
                $action[1] = ($query[2] ?? 'index');
                $action[2] = $query[3] ?? 'index';

                $area = Core\Controller\AreaEnum::AREA_ADMIN;
            } else {
                $action[0] = $query[0];
                $action[1] = $query[1] ?? 'index';
                $action[2] = $query[2] ?? 'index';

                $area = Core\Controller\AreaEnum::AREA_FRONTEND;
            }

            $permissionPath = $area . '/' . $action[0] . '/' . $action[1] . '/' . $action[2];

            if ($this->acl->hasPermission($permissionPath) === true) {
                return $this->collectData($params, $action, $area);
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
            $langArray = \explode('|', $params['lang']);
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
