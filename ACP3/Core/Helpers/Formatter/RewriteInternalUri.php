<?php
namespace ACP3\Core\Helpers\Formatter;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule;

/**
 * Class RewriteInternalUri
 * @package ACP3\Core\Helpers\Formatter
 */
class RewriteInternalUri
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Modules\Helper\ControllerActionExists
     */
    protected $controllerActionExists;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule
     */
    protected $uriAliasValidationRule;

    /**
     * RewriteInternalUri constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath                                   $appPath
     * @param \ACP3\Core\Modules\Helper\ControllerActionExists                         $controllerActionExists
     * @param \ACP3\Core\Http\RequestInterface                                         $request
     * @param \ACP3\Core\RouterInterface                                               $router
     * @param \ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule $uriAliasValidationRule
     */
    public function __construct(
        Core\Environment\ApplicationPath $appPath,
        Core\Modules\Helper\ControllerActionExists $controllerActionExists,
        Core\Http\RequestInterface $request,
        Core\RouterInterface $router,
        UriAliasValidationRule $uriAliasValidationRule
    ) {
        $this->appPath = $appPath;
        $this->controllerActionExists = $controllerActionExists;
        $this->request = $request;
        $this->router = $router;
        $this->uriAliasValidationRule = $uriAliasValidationRule;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function rewriteInternalUri($text)
    {
        $rootDir = str_replace('/', '\/', $this->appPath->getWebRoot());
        $host = $this->request->getServer()->get('HTTP_HOST');
        $pattern = '/<a([^>]+)href="(http(s?):\/\/' . $host . ')?(' . $rootDir . ')?(index\.php)?(\/?)((?i:[a-z\d_\-]+\/){2,})"/i';

        return preg_replace_callback(
            $pattern,
            [$this, "rewriteInternalUriCallback"],
            $text
        );
    }

    /**
     * @param array $matches
     *
     * @return string
     */
    private function rewriteInternalUriCallback(array $matches)
    {
        if ($this->uriAliasValidationRule->isValid($matches[7]) !== true) {
            $resourceParts = explode('/', $matches[7]);
            $path = $this->getResourcePath($resourceParts);
            if ($this->controllerActionExists->controllerActionExists($path) === true) {
                return '<a' . $matches[1] . 'href="' . $this->router->route($matches[7]) . '"';
            }
        }

        return $matches[0];
    }

    /**
     * @param array $resourceParts
     *
     * @return string
     */
    private function getResourcePath(array $resourceParts)
    {
        $path = 'frontend/' . $resourceParts[0];
        if (!empty($resourceParts[1])) {
            $path .= '/' . $resourceParts[1];
        }
        if (!empty($resourceParts[2])) {
            $path .= '/' . $resourceParts[2];
        }
        if (!empty($resourceParts[3])) {
            $path .= '/' . $resourceParts[3];
        }

        return $path;
    }
}
