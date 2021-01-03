<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use Psr\Container\ContainerInterface;

class CaptchaTypeValidationRule extends AbstractValidationRule
{
    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $captchaLocator;

    public function __construct(ContainerInterface $captchaLocator)
    {
        $this->captchaLocator = $captchaLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->captchaLocator->has($data);
    }
}
