<?php
namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\Validation\Validator;

/**
 * Interface ValidationRuleInterface
 * @package ACP3\Core\Validation\ValidationRules
 */
interface ValidationRuleInterface
{
    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message);

    /**
     * @param \ACP3\Core\Validation\Validator $validator
     * @param mixed                           $data
     * @param string                          $field
     * @param array                           $extra
     *
     * @return
     */
    public function validate(Validator $validator, $data, $field = '', array $extra = []);

    /**
     * @param mixed  $data
     * @param string $field
     * @param array  $extra
     *
     * @return boolean
     */
    public function isValid($data, $field = '', array $extra = []);
}