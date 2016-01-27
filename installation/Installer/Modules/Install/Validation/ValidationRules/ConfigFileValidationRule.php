<?php
namespace ACP3\Installer\Modules\Install\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;

/**
 * Class ConfigFileValidationRule
 * @package ACP3\Installer\Modules\Install\Validation\ValidationRules
 */
class ConfigFileValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        return is_file($data) === true && is_writable($data) === true;
    }
}