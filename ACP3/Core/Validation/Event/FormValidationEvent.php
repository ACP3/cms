<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Validation\Event;


use ACP3\Core\Validation\Validator;
use Symfony\Component\EventDispatcher\Event;

class FormValidationEvent extends Event
{
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var array
     */
    private $formData;
    /**
     * @var array
     */
    private $extra;

    /**
     * FormValidationEvent constructor.
     * @param Validator $validator
     * @param array $formData
     * @param array $extra
     */
    public function __construct(
        Validator $validator,
        array $formData,
        array $extra = []
    ) {
        $this->validator = $validator;
        $this->formData = $formData;
        $this->extra = $extra;
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @return array
     */
    public function getFormData()
    {
        return $this->formData;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }
}
