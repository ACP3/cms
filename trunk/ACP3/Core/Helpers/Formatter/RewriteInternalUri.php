<?php
namespace ACP3\Core\Helpers\Formatter;

use ACP3\Core;

/**
 * Class RewriteInternalUri
 * @package ACP3\Core\Helpers\Formatter
 */
class RewriteInternalUri
{
    /**
     * @var Core\Modules
     */
    protected $modules;

    /**
     * @var Core\Router
     */
    protected $router;
    /**
     * @var Core\Validator\Rules\Router\Aliases
     */
    protected $aliasesValidator;

    /**
     * @param Core\Modules $modules
     * @param Core\Router $router
     * @param Core\Validator\Rules\Router\Aliases $aliasValidator
     */
    public function __construct(
        Core\Modules $modules,
        Core\Router $router,
        Core\Validator\Rules\Router\Aliases $aliasValidator
    ) {
        $this->modules = $modules;
        $this->router = $router;
        $this->aliasesValidator = $aliasValidator;
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
        $host = $_SERVER['HTTP_HOST'];
        return preg_replace_callback('/<a href="(http(s?):\/\/' . $host . ')?(' . $rootDir . ')?(index\.php)?(\/?)((?i:[a-z\d_\-]+\/){2,})"/', [$this, "_rewriteInternalUriCallback"], $text);
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
        if ($this->aliasesValidator->uriAliasExists($matches[6]) === true) {
            return $matches[0];
        } else {
            $uriArray = explode('/', $matches[6]);
            $path = 'frontend/' . $uriArray[0];
            if (!empty($uriArray[1])) {
                $path .= '/' . $uriArray[1];
            }
            if (!empty($uriArray[2])) {
                $path .= '/' . $uriArray[2];
            }

            if ($this->modules->actionExists($path)) {
                return '<a href="' . $this->router->route($matches[6]) . '"';
            } else {
                return $matches[0];
            }
        }
    }
}
