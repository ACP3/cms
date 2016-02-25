<?php
namespace ACP3\Modules\ACP3\Files\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;

/**
 * Class IsExternalFileValidationRule
 * @package ACP3\Modules\ACP3\Files\Validation\ValidationRules
 */
class IsExternalFileValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && is_array($field)) {
            $external = reset($field);
            $filesize = next($field);
            $unit = next($field);

            $file = isset($extra['file']) ? $extra['file'] : null;

            return !(isset($data[$external]) && (empty($file) || empty($data[$filesize]) || empty($data[$unit])));
        }

        return false;
    }
}
