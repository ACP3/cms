<?php
namespace ACP3\Modules\ACP3\Comments\Validator\ValidationRules;

use ACP3\Core\Validator\ValidationRules\AbstractValidationRule;
use ACP3\Core\Validator\ValidationRules\IntegerValidationRule;

/**
 * Class UserNameValidationRule
 * @package ACP3\Modules\ACP3\Comments\Validator\ValidationRules
 */
class UserNameValidationRule extends AbstractValidationRule
{
    const NAME = 'comments_user_name';

    /**
     * @var \ACP3\Core\Validator\ValidationRules\IntegerValidationRule
     */
    protected $integerValidationRule;

    /**
     * UserNameValidationRule constructor.
     *
     * @param \ACP3\Core\Validator\ValidationRules\IntegerValidationRule $integerValidationRule
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