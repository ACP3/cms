<?php
namespace ACP3\Core\Validation;

use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException;
use ACP3\Core\Validation\ValidationRules\ValidationRuleInterface;

/**
 * Class Validator
 * @package ACP3\Core\Validation
 */
class Validator
{
    /**
     * @var \ACP3\Core\Validation\ValidationRules\ValidationRuleInterface[]
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
     * @param \ACP3\Core\Validation\ValidationRules\ValidationRuleInterface $validationRule
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
    public function addConstraint($validationRuleName, array $params = [])
    {
        $this->constraints[] = [
            'rule' => $validationRuleName,
            'params' => array_merge($this->getDefaultConstraintParams(), $params)
        ];

        return $this;
    }

    /**
     * @param string       $message
     * @param string|array $field
     *
     * @return $this
     */
    public function addError($message, $field = '')
    {
        if (!empty($field)) {
            $fieldName = $this->mapField($field);
            $this->errors[$fieldName] = $message;
        } else {
            $this->errors[] = $message;
        }

        return $this;
    }

    /**
     * @param string|array $field
     *
     * @return string
     */
    protected function mapField($field)
    {
        if (is_array($field)) {
            $field = reset($field);
        }

        return str_replace('_', '-', $field);
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
            } else {
                throw new ValidationRuleNotFoundException('Can not find the validation rule with the name ' . $constraint['rule'] . '.');
            }
        }

        if ($this->hasErrors()) {
            throw new ValidationFailedException($this->errors);
        }
    }

    /**
     * @param string $validationRuleName
     * @param mixed  $field
     *
     * @return bool
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     */
    public function is($validationRuleName, $field)
    {
        if (isset($this->validationRules[$validationRuleName])) {
            return $this->validationRules[$validationRuleName]->isValid($field);
        }

        throw new ValidationRuleNotFoundException('Can not find the validation rule with the name ' . $validationRuleName . '.');
    }

    /**
     * @return bool
     */
    protected function hasErrors()
    {
        return !empty($this->errors);
    }
}