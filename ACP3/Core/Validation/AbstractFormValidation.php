<?php
namespace ACP3\Core\Validation;

use ACP3\Core;

/**
 * Class AbstractFormValidation
 * @package ACP3\Core\Validation
 */
class AbstractFormValidation
{
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Validation\Validator
     */
    protected $validator;

    /**
     * @param \ACP3\Core\Lang                 $lang
     * @param \ACP3\Core\Validation\Validator $validator
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validation\Validator $validator
    )
    {
        $this->lang = $lang;
        $this->validator = $validator;
    }
}