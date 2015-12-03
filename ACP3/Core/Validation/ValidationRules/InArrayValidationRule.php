<?php
namespace ACP3\Core\Validation\ValidationRules;

/**
 * Class InArrayValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
class InArrayValidationRule extends AbstractValidationRule
{
    const NAME = 'in_array';

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkInArray($data, $extra['haystack']);
    }

    /**
     * @param string $needle
     * @param array  $haystack
     *
     * @return bool
     */
    protected function checkInArray($needle, array $haystack)
    {
        return in_array($needle, $haystack);
    }
}