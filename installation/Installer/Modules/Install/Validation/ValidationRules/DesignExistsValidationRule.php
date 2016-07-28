<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Modules\Install\Validation\ValidationRules;


use ACP3\Core\Validation\ValidationRules\AbstractValidationRule;

/**
 * Class DesignExistsValidationRule
 * @package ACP3\Installer\Modules\Install\Validation\ValidationRules
 */
class DesignExistsValidationRule extends AbstractValidationRule
{

    /**
     * @param mixed $data
     * @param string $field
     * @param array $extra
     *
     * @return boolean
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return $this->checkDesignExists($data);
    }

    /**
     * @param string $design
     * @return bool
     */
    private function checkDesignExists($design)
    {
        $path = ACP3_ROOT_DIR . 'designs/' . $design . '/info.xml';

        return !preg_match('=/=', $design) && is_file($path) === true;
    }
}
