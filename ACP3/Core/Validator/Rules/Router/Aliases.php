<?php
namespace ACP3\Core\Validator\Rules\Router;

use ACP3\Core;
use ACP3\Modules\Seo;

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
     * @var \ACP3\Modules\Seo\Model
     */
    protected $seoModel;

    /**
     * @param \ACP3\Core\Validator\Rules\Router $routerValidator
     * @param \ACP3\Modules\Seo\Model           $seoModel
     */
    public function __construct(
        Core\Validator\Rules\Router $routerValidator,
        Seo\Model $seoModel
    ) {
        $this->routerValidator = $routerValidator;
        $this->seoModel = $seoModel;
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
                    return $this->seoModel->uriAliasExistsByAlias($alias, $path);
                } elseif ($this->seoModel->uriAliasExistsByAlias($alias) === true) {
                    return true;
                }
            }
        }
        return false;
    }
}
