<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core\ACL;
use ACP3\Core\Environment\ApplicationMode;
use ACP3\Core\Router\RouterInterface;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

class LoadModule extends AbstractFunction
{
    public function __construct(private ACL $acl, private RouterInterface $router, private FragmentHandler $fragmentHandler, private string $applicationMode)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): mixed
    {
        $pathArray = $this->convertPathToArray($params['module']);
        $path = $pathArray[0] . '/' . $pathArray[1] . '/' . $pathArray[2] . '/' . $pathArray[3];

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
            static fn ($item) => urlencode($item),
            $arguments
        );
    }

    /**
     * @param array<string, string> $arguments
     */
    private function esiInclude(string $path, array $arguments): string
    {
        $routeArguments = '';
        foreach ($arguments as $key => $value) {
            $routeArguments .= '/' . $key . '_' . $value;
        }

        return $this->fragmentHandler->render(
            $this->router->route($path . $routeArguments, true),
            'esi',
            [
                'ignore_errors' => $this->applicationMode === ApplicationMode::PRODUCTION,
            ]
        );
    }
}
