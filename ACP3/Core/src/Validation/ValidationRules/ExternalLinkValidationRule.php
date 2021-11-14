<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

class ExternalLinkValidationRule extends AbstractValidationRule
{
    public function __construct(private InArrayValidationRule $inArrayValidationRule)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \is_array($field)) {
            $linkTitle = reset($field);
            $uri = next($field);
            $target = next($field);

            return $this->isValidLink($data[$linkTitle], $data[$uri], $data[$target]);
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
