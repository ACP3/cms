<?php
namespace ACP3\Modules\ACP3\Seo\Validation\ValidationRules;

use ACP3\Core;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Seo;

/**
 * Class UriAliasValidationRule
 * @package ACP3\Modules\ACP3\Seo\Validation\ValidationRules
 */
class UriAliasValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Validation\ValidationRules\InternalUriValidationRule
     */
    protected $internalUriValidationRule;
    /**
     * @var \ACP3\Core\Validation\ValidationRules\UriSafeValidationRule
     */
    protected $uriSafeValidationRule;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository
     */
    protected $seoRepository;

    /**
     * UriAliasValidationRule constructor.
     *
     * @param \ACP3\Core\Environment\ApplicationPath                          $appPath
     * @param \ACP3\Core\Validation\ValidationRules\InternalUriValidationRule $internalUriValidationRule
     * @param \ACP3\Core\Validation\ValidationRules\UriSafeValidationRule     $uriSafeValidationRule
     * @param \ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository                      $seoRepository
     */
    public function __construct(
        Core\Environment\ApplicationPath $appPath,
        Core\Validation\ValidationRules\InternalUriValidationRule $internalUriValidationRule,
        Core\Validation\ValidationRules\UriSafeValidationRule $uriSafeValidationRule,
        Seo\Model\Repository\SeoRepository $seoRepository
    ) {
        $this->appPath = $appPath;
        $this->internalUriValidationRule = $internalUriValidationRule;
        $this->uriSafeValidationRule = $uriSafeValidationRule;
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

        if ($this->uriSafeValidationRule->isValid($alias)) {
            if (is_dir($this->appPath->getModulesDir() . $alias) === true) {
                return false;
            }

            $path .= !preg_match('=/$=', $path) ? '/' : '';
            if ($path !== '/' && $this->internalUriValidationRule->isValid($path) === false) {
                return false;
            }

            return !$this->seoRepository->uriAliasExistsByAlias($alias, $path);
        }
        return false;
    }
}
