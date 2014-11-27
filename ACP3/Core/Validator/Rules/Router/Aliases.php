<?php
namespace ACP3\Core\Validator\Rules\Router;

use ACP3\Core;
use ACP3\Modules\System;

/**
 * Class Aliases
 * @package ACP3\Core\Validator\Rules\Router
 */
class Aliases
{
    /**
     * @var Core\Validator\Rules\Router
     */
    protected $routerValidator;
    /**
     * @var System\Model
     */
    protected $systemModel;

    /**
     * @param Core\Validator\Rules\Router $routerValidator
     * @param System\Model $systemModel
     */
    public function __construct(
        Core\Validator\Rules\Router $routerValidator,
        System\Model $systemModel
    ) {
        $this->routerValidator = $routerValidator;
        $this->systemModel = $systemModel;
    }

    /**
     * Überprüft, ob ein URI-Alias bereits existiert
     *
     * @param string $alias
     * @param string $path
     *
     * @return boolean
     */
    public function uriAliasExists($alias, $path = '')
    {
        if ($this->routerValidator->isUriSafe($alias)) {
            if (is_dir(MODULES_DIR . $alias) === true) {
                return true;
            } else {
                $path .= !preg_match('=/$=', $path) ? '/' : '';
                if ($path !== '/' && $this->routerValidator->isInternalURI($path) === true) {
                    return $this->systemModel->uriAliasExistsByAlias($alias, $path);
                } elseif ($this->systemModel->uriAliasExistsByAlias($alias) === true) {
                    return true;
                }
            }
        }
        return false;
    }
}
