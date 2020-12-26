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
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var string
     */
    private $applicationMode;
    /**
     * @var \Symfony\Component\HttpKernel\Fragment\FragmentHandler
     */
    private $fragmentHandler;

    public function __construct(
        ACL $acl,
        RouterInterface $router,
        FragmentHandler $fragmentHandler,
        string $applicationMode
    ) {
        $this->acl = $acl;
        $this->router = $router;
        $this->applicationMode = $applicationMode;
        $this->fragmentHandler = $fragmentHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): string
    {
        $pathArray = $this->convertPathToArray($params['module']);
        $path = $pathArray[0] . '/' . $pathArray[1] . '/' . $pathArray[2] . '/' . $pathArray[3];

        $response = '';
        if ($this->acl->hasPermission($path) === true) {
            $response = $this->esiInclude($path, $this->parseControllerActionArguments($params));
        }

        return $response;
    }

    protected function convertPathToArray(string $resource): array
    {
        $pathArray = \explode('/', \strtolower($resource));

        if (empty($pathArray[2]) === true) {
            $pathArray[2] = 'index';
        }
        if (empty($pathArray[3]) === true) {
            $pathArray[3] = 'index';
        }

        return $pathArray;
    }

    private function parseControllerActionArguments(array $arguments): array
    {
        if (isset($arguments['args']) && \is_array($arguments['args'])) {
            return $this->urlEncodeArguments($arguments['args']);
        }

        unset($arguments['module']);

        return $this->urlEncodeArguments($arguments);
    }

    private function urlEncodeArguments(array $arguments): array
    {
        return \array_map(
            static function ($item) {
                return \urlencode($item);
            },
            $arguments
        );
    }

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
