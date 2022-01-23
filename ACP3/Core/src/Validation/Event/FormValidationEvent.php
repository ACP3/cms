<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\Event;

use ACP3\Core\Validation\Validator;
use Symfony\Contracts\EventDispatcher\Event;

class FormValidationEvent extends Event
{
    /**
     * @param array<string, mixed> $formData
     * @param mixed[]              $extra
     */
    public function __construct(private Validator $validator, private array $formData, private array $extra = [])
    {
    }

    public function getValidator(): Validator
    {
        return $this->validator;
    }

    /**
     * @return array<string, mixed>
     */
    public function getFormData(): array
    {
        return $this->formData;
    }

    /**
     * @return mixed[]
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
