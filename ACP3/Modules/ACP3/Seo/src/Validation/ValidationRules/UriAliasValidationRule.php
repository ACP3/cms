<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Validation\ValidationRules;

use ACP3\Core;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Seo;

class UriAliasValidationRule extends AbstractValidationRule
{
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

    public function __construct(
        Core\Validation\ValidationRules\InternalUriValidationRule $internalUriValidationRule,
        Core\Validation\ValidationRules\UriSafeValidationRule $uriSafeValidationRule,
        Seo\Model\Repository\SeoRepository $seoRepository
    ) {
        $this->internalUriValidationRule = $internalUriValidationRule;
        $this->uriSafeValidationRule = $uriSafeValidationRule;
        $this->seoRepository = $seoRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkUriAlias($data, $extra['path'] ?? '');
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
            $path .= !preg_match('=/$=', $path) ? '/' : '';
            if ($path !== '/' && $this->internalUriValidationRule->isValid($path) === false) {
                return false;
            }

            return !$this->seoRepository->uriAliasExistsByAlias($alias, $path);
        }

        return false;
    }
}
