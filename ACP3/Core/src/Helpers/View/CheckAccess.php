<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\View;

use ACP3\Core\ACL;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Router\RouterInterface;

class CheckAccess
{
    public function __construct(private readonly ACL $acl, private readonly Translator $translator, private readonly RouterInterface $router)
    {
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>|string
     */
    public function outputLinkOrButton(array $params): array|string
    {
        if (isset($params['mode'], $params['path'])) {
            $action = $this->completeControllerAction($params['path']);
            $area = $this->getArea($params['path']);

            $permissionPath = $area->value . '/' . $action[0] . '/' . $action[1] . '/' . $action[2];

            if ($this->acl->hasPermission($permissionPath) === true) {
                return $this->collectData($params, $action, $area);
            }

            if ($params['mode'] === 'link' && isset($params['title'])) {
                // If the user has no permission and the type is "link",
                // just return the given title without the surrounding hyperlink
                return $params['title'];
            }
        }

        return '';
    }

    /**
     * @return string[]
     */
    private function completeControllerAction(string $path): array
    {
        $action = [];

        $query = explode('/', strtolower($path));

        if (isset($query[0]) && $query[0] === 'acp') {
            $action[0] = $query[1] ?? 'acp';
            $action[1] = $query[2] ?? 'index';
            $action[2] = $query[3] ?? 'index';
        } else {
            $action[0] = $query[0];
            $action[1] = $query[1] ?? 'index';
            $action[2] = $query[2] ?? 'index';
        }

        return $action;
    }

    private function getArea(string $path): AreaEnum
    {
        $query = explode('/', strtolower($path));

        if (isset($query[0]) && $query[0] === 'acp') {
            return AreaEnum::AREA_ADMIN;
        }

        return AreaEnum::AREA_FRONTEND;
    }

    /**
     * @param array<string, mixed> $params
     * @param string[]             $action
     *
     * @return array<string, mixed>
     */
    private function collectData(array $params, array $action, AreaEnum $area): array
    {
        if (isset($params['lang'])) {
            $langArray = explode('|', (string) $params['lang']);

            $lang = $this->translator->t($langArray[0], $langArray[1]);
        } else {
            $lang = $this->translator->t($action[0], $area->value . '_' . $action[1] . '_' . $action[2]);
        }

        return [
            'iconSet' => $params['iconSet'] ?? null,
            'icon' => $params['icon'] ?? null,
            'mode' => $params['mode'] ?? null,
            'uri' => $this->router->route($params['path']),
            'title' => $params['title'] ?? null,
            'class' => $params['class'] ?? null,
            'blank' => $params['blank'] ?? false,
            'selectors' => $params['selectors'] ?? '',
            'lang' => $lang,
        ];
    }
}
