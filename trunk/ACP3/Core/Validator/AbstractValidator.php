<?php
namespace ACP3\Core\Validation;

use ACP3\Core;

/**
 * Class AbstractValidator
 * @package ACP3\Core\Validation
 */
class AbstractValidator
{
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;

    public function __construct(Core\Lang $lang)
    {
        $this->lang = $lang;
    }

    /**
     * @throws Core\Exceptions\InvalidFormToken
     */
    public function validateFormKey()
    {
        if (Core\Validate::formToken() === false) {
            throw new Core\Exceptions\InvalidFormToken($this->lang->t('system', 'form_already_submitted'));
        }
    }

} 