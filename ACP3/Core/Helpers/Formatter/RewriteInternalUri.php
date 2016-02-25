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
     * @var \ACP3\Core\Http\Request
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
     * @param \ACP3\Core\Http\Request                                                  $request
     * @param \ACP3\Core\RouterInterface                                               $router
     * @param \ACP3\Modules\ACP3\Seo\Validation\ValidationRules\UriAliasValidationRule $uriAliasValidationRule
     */
    public function __construct(
        Core\Environment\ApplicationPath $appPath,
        Core\Modules\Helper\ControllerActionExists $controllerActionExists,
        Core\Http\Request $request,
        Core\RouterInterface $router,
        UriAliasValidationRule $uriAliasValidationRule
    )
    {
        $this->appPath = $appPath;
        $this->controllerActionExists = $controllerActionExists;
        $this->request = $request;
        $this->router = $router;
        $this->uriAliasValidationRule = $uriAliasValidationRule;
    }

    /**
     * Ersetzt interne ACP3 interne URIs in Texten mit ihren jeweiligen Aliasen
     *
     * @param string $text
     *
     * @return string
     */
    public function rewriteInternalUri($text)
    {
        $rootDir = str_replace('/', '\/', $this->appPath->getWebRoot());
        $host = $this->request->getServer()->get('HTTP_HOST');
        return preg_replace_callback(
            '/<a([^>]+)href="(http(s?):\/\/' . $host . ')?(' . $rootDir . ')?(index\.php)?(\/?)((?i:[a-z\d_\-]+\/){2,})"/i',
            [$this, "rewriteInternalUriCallback"],
            $text
        );
    }

    /**
     * Callback-Funktion zum Ersetzen der ACP3 internen URIs gegen ihre Aliase
     *
     * @param array $matches
     *
     * @return string
     */
    private function rewriteInternalUriCallback(array $matches)
    {
        if ($this->uriAliasValidationRule->isValid($matches[7]) === true) {
            return $matches[0];
        } else {
            $uriArray = explode('/', $matches[7]);
            $path = 'frontend/' . $uriArray[0];
            if (!empty($uriArray[1])) {
                $path .= '/' . $uriArray[1];
            }
            if (!empty($uriArray[2])) {
                $path .= '/' . $uriArray[2];
            }
            if (!empty($uriArray[3])) {
                $path .= '/' . $uriArray[3];
            }

            if ($this->controllerActionExists->controllerActionExists($path) === true) {
                return '<a' . $matches[1] . 'href="' . $this->router->route($matches[7]) . '"';
            } else {
                return $matches[0];
            }
        }
    }
}
