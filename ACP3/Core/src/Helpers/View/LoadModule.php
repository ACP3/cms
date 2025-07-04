<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\View;

use ACP3\Core\ACL;
use ACP3\Core\Controller\AreaEnum;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class LoadModule
{
    public function __construct(
        private readonly ACL $acl,
        private readonly FragmentHandler $fragmentHandler)
    {
    }

    /**
     * @param array<string, mixed> $params
     */
    public function __invoke(string $modulePath, array $params): string
    {
        [$area, $module, $controller, $action] = $this->convertPathToArray($modulePath);
        $path = $area . '/' . $module . '/' . $controller . '/' . $action;

        $response = '';
        if ($this->acl->hasPermission($path) === true) {
            $response = $this->esiInclude($path, $this->urlEncodeArguments($params));
        }

        return $response;
    }

    /**
     * @return string[]
     */
    protected function convertPathToArray(string $resource): array
    {
        $pathArray = explode('/', strtolower($resource));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        return $pathArray;
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return string[]
     */
    private function urlEncodeArguments(array $arguments): array
    {
        return array_map(
            static fn ($item) => urlencode((string) $item),
            $arguments
        );
    }

    /**
     * @param array<string, string> $arguments
     */
    private function esiInclude(string $path, array $arguments): string
    {
        [$area, $module, $controller, $action] = explode('/', $path);

        if ($area === AreaEnum::AREA_ADMIN->value) {
            $path = 'acp/' . $module . '/' . $controller . '/' . $action;
        } elseif ($area === AreaEnum::AREA_FRONTEND->value) {
            $path = $module . '/' . $controller . '/' . $action;
        }

        $routeArguments = '';
        foreach ($arguments as $key => $value) {
            $routeArguments .= '/' . $key . '_' . $value;
        }

        return $this->fragmentHandler->render(
            '/' . $path . $routeArguments,
            'esi',
        );
    }
}
