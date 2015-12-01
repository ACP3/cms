<?php
namespace ACP3\Core\Helpers\Formatter;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo\Validator\ValidationRules\UriAliasValidationRule;

/**
 * Class RewriteInternalUri
 * @package ACP3\Core\Helpers\Formatter
 */
class RewriteInternalUri
{
    /**
     * @var \ACP3\Core\Modules\Helper\ControllerActionExists
     */
    protected $controllerActionExists;
    /**
     * @var \ACP3\Core\Http\Request
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Validator\ValidationRules\UriAliasValidationRule
     */
    protected $uriAliasValidationRule;

    /**
     * @param \ACP3\Core\Modules\Helper\ControllerActionExists                        $controllerActionExists
     * @param \ACP3\Core\Http\Request                                                 $request
     * @param \ACP3\Core\Router                                                       $router
     * @param \ACP3\Modules\ACP3\Seo\Validator\ValidationRules\UriAliasValidationRule $uriAliasValidationRule
     */
    public function __construct(
        Core\Modules\Helper\ControllerActionExists $controllerActionExists,
        Core\Http\Request $request,
        Core\Router $router,
        UriAliasValidationRule $uriAliasValidationRule
    )
    {
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
        $rootDir = str_replace('/', '\/', ROOT_DIR);
        $host = $this->request->getServer()->get('HTTP_HOST');
        return preg_replace_callback(
            '/<a([^>]+)href="(http(s?):\/\/' . $host . ')?(' . $rootDir . ')?(index\.php)?(\/?)((?i:[a-z\d_\-]+\/){2,})"/i',
            [$this, "_rewriteInternalUriCallback"],
            $text
        );
    }

    /**
     * Callback-Funktion zum Ersetzen der ACP3 internen URIs gegen ihre Aliase
     *
     * @param string $matches
     *
     * @return string
     */
    private function _rewriteInternalUriCallback($matches)
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
