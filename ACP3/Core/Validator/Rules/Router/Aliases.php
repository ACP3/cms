<?php
namespace ACP3\Core\Validator\Rules\Router;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;

/**
 * Class Aliases
 * @package ACP3\Core\Validator\Rules\Router
 */
class Aliases
{
    /**
     * @var \ACP3\Core\Validator\Rules\Router
     */
    protected $routerValidator;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\SeoRepository
     */
    protected $seoRepository;

    /**
     * @param \ACP3\Core\Validator\Rules\Router          $routerValidator
     * @param \ACP3\Modules\ACP3\Seo\Model\SeoRepository $seoRepository
     */
    public function __construct(
        Core\Validator\Rules\Router $routerValidator,
        Seo\Model\SeoRepository $seoRepository
    )
    {
        $this->routerValidator = $routerValidator;
        $this->seoRepository = $seoRepository;
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
                    return $this->seoRepository->uriAliasExistsByAlias($alias, $path);
                } elseif ($this->seoRepository->uriAliasExistsByAlias($alias) === true) {
                    return true;
                }
            }
        }
        return false;
    }
}
