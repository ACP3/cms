<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\ACL;
use ACP3\Core\Controller\AreaEnum;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class LoadModule extends AbstractFunction
{
    public function __construct(
        private readonly ACL $acl,
        private readonly FragmentHandler $fragmentHandler)
    {
    }

    public function __invoke(array $params, \Smarty_Internal_Template $smarty): string
    {
        [$area, $module, $controller, $action] = $this->convertPathToArray($params['module']);
        $path = $area . '/' . $module . '/' . $controller . '/' . $action;

        $response = '';
        if ($this->acl->hasPermission($path) === true) {
            $response = $this->esiInclude($path, $this->parseControllerActionArguments($params));
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
    private function parseControllerActionArguments(array $arguments): array
    {
        if (isset($arguments['args']) && \is_array($arguments['args'])) {
            return $this->urlEncodeArguments($arguments['args']);
        }

        unset($arguments['module']);

        return $this->urlEncodeArguments($arguments);
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
