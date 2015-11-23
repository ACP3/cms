<?php
namespace ACP3\Core\Validator;

use ACP3\Core\Exceptions\ValidationFailed;
use ACP3\Core\Validator\ValidationRules\ValidationRuleInterface;

/**
 * Class Validator
 * @package ACP3\Core\Validator
 */
class Validator
{
    /**
     * @var \ACP3\Core\Validator\ValidationRules\ValidationRuleInterface[]
     */
    protected $validationRules = [];
    /**
     * @var array
     */
    protected $errors = [];
    /**
     * @var array
     */
    protected $constraints = [];

    /**
     * @param \ACP3\Core\Validator\ValidationRules\ValidationRuleInterface $validationRule
     *
     * @return $this
     */
    public function registerValidationRule(ValidationRuleInterface $validationRule)
    {
        $this->validationRules[$validationRule->getName()] = $validationRule;

        return $this;
    }

    /**
     * @return array
     */
    protected function getDefaultConstraintParams()
    {
        return [
            'data' => null,
            'field' => '',
            'message' => '',
            'extra' => []
        ];
    }

    /**
     * @param string $validationRuleName
     * @param array  $params
     *
     * @return $this
     */
    public function addConstraint($validationRuleName, array $params)
    {
        $this->constraints[] = [
            'rule' => $validationRuleName,
            'params' => array_merge($this->getDefaultConstraintParams(), $params)
        ];

        return $this;
    }

    /**
     * @param string $message
     * @param string $field
     *
     * @return $this
     */
    public function addError($message, $field = '')
    {
        if (!empty($field)) {
            $this->errors[$field] = $message;
        } else {
            $this->errors[] = $message;
        }

        return $this;
    }

    public function validate()
    {
        $this->errors = [];

        foreach ($this->constraints as $constraint) {
            if (isset($this->validationRules[$constraint['rule']])) {
                $validationRule = $this->validationRules[$constraint['rule']];
                $params = $constraint['params'];

                if (!empty($params['message'])) {
                    $validationRule->setMessage($params['message']);
                }

                $validationRule->validate(
                    $this,
                    $params['data'],
                    $params['field'],
                    $params['extra']
                );
            }
        }

        if ($this->hasErrors()) {
            throw new ValidationFailed($this->errors);
        }
    }

    /**
     * @param string $validationRuleName
     * @param mixed  $field
     *
     * @return bool
     */
    public function is($validationRuleName, $field)
    {
        if (isset($this->validationRules[$validationRuleName])) {
            return $this->validationRules[$validationRuleName]->isValid($field);
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function hasErrors()
    {
        return !empty($this->errors);
    }
}