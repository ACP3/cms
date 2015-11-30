<?php
namespace ACP3\Installer\Modules\Install\Validator\ValidationRules;

use ACP3\Core\Validator\ValidationRules\AbstractValidationRule;

/**
 * Class ConfigFileValidationRule
 * @package ACP3\Installer\Modules\Install\Validator\ValidationRules
 */
class ConfigFileValidationRule extends AbstractValidationRule
{
    const NAME = 'installer_config_file';

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        return is_file($data) === true && is_writable($data) === true;
    }
}