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
     * @var array
     */
    private $formData;
    /**
     * @var array
     */
    private $extra;

    public function __construct(
        private Validator $validator,
        array $formData,
        array $extra = []
    ) {
        $this->formData = $formData;
        $this->extra = $extra;
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

    public function getExtra(): array
    {
        return $this->extra;
    }
}
