<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Validation\ValidationRules;

use ACP3\Core;
use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Modules\ACP3\Seo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UriAliasValidationRule extends AbstractValidationRule
{
    public function __construct(protected Core\Validation\ValidationRules\InternalUriValidationRule $internalUriValidationRule, protected Core\Validation\ValidationRules\UriSafeValidationRule $uriSafeValidationRule, protected Seo\Repository\SeoRepository $seoRepository)
    {
    }

    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkUriAlias($data, $extra['path'] ?? '');
    }

    private function checkUriAlias(string $alias, string $path): bool
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
