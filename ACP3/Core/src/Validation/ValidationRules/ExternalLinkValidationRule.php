<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ExternalLinkValidationRule extends AbstractValidationRule
{
    public function __construct(private readonly InArrayValidationRule $inArrayValidationRule)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(bool|int|float|string|array|UploadedFile|null $data, string|array $field = '', array $extra = []): bool
    {
        if (\is_array($data) && \is_array($field)) {
            $linkTitle = reset($field);
            $uri = next($field);
            $target = next($field);

            return $this->isValidLink($data[$linkTitle], $data[$uri], (int) $data[$target]);
        }

        return false;
    }

    protected function isValidLink(string $linkTitle, string $uri, int $target): bool
    {
        if (empty($linkTitle)) {
            return true;
        }

        return !empty($uri) && $this->inArrayValidationRule->isValid($target, '', ['haystack' => [1, 2]]);
    }
}
