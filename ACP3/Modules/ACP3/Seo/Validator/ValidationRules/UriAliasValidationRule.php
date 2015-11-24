<?php
namespace ACP3\Modules\ACP3\Seo\Validator\ValidationRules;

use ACP3\Core;
use ACP3\Core\Validator\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Seo;

/**
 * Class UriAliasValidationRule
 * @package ACP3\Modules\ACP3\Seo\Validator\ValidationRules
 */
class UriAliasValidationRule extends AbstractValidationRule
{
    const NAME = 'seo_uri_alias';

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
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkUriAlias($data, isset($extra['path']) ? $extra['path'] : '');
    }

    /**
     * @param string $alias
     * @param string $path
     *
     * @return bool
     */
    protected function checkUriAlias($alias, $path)
    {
        if (empty($alias)) {
            return true;
        }

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