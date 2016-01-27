<?php
namespace ACP3\Modules\ACP3\Comments\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;
use ACP3\Core\Validation\ValidationRules\IntegerValidationRule;

/**
 * Class UserNameValidationRule
 * @package ACP3\Modules\ACP3\Comments\Validation\ValidationRules
 */
class UserNameValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\Validation\ValidationRules\IntegerValidationRule
     */
    protected $integerValidationRule;

    /**
     * UserNameValidationRule constructor.
     *
     * @param \ACP3\Core\Validation\ValidationRules\IntegerValidationRule $integerValidationRule
     */
    public function __construct(IntegerValidationRule $integerValidationRule)
    {
        $this->integerValidationRule = $integerValidationRule;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && is_array($field)) {
            $userName = reset($field);
            $userId = next($field);

            return (!empty($data[$userId]) && $this->integerValidationRule->isValid($data[$userId])) || !empty($data[$userName]);
        };

        return false;
    }
}