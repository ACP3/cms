<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation;

use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException;
use ACP3\Core\Validation\ValidationRules\ValidationRuleInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Validator
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
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
     * Validator constructor.
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \ACP3\Core\Validation\ValidationRules\ValidationRuleInterface $validationRule
     *
     * @return $this
     */
    public function registerValidationRule(ValidationRuleInterface $validationRule)
    {
        $this->validationRules[\get_class($validationRule)] = $validationRule;

        return $this;
    }

    /**
     * @param string $validationRule
     * @param array  $params
     *
     * @return $this
     */
    public function addConstraint($validationRule, array $params = [])
    {
        $this->constraints[] = [
            'rule' => $validationRule,
            'params' => \array_merge($this->getDefaultConstraintParams(), $params),
        ];

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
            'extra' => [],
        ];
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
        if (\is_array($field)) {
            $field = \reset($field);
        }

        return \str_replace('_', '-', $field);
    }

    /**
     * @param string $eventName
     * @param array $formData
     * @param array $extra
     */
    public function dispatchValidationEvent($eventName, array $formData, array $extra = [])
    {
        $this->eventDispatcher->dispatch($eventName, new FormValidationEvent($this, $formData, $extra));
    }

    /**
     * Validates a form
     *
     * @throws ValidationFailedException
     * @throws ValidationRuleNotFoundException
     */
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
                throw new ValidationRuleNotFoundException(\sprintf($this->getExceptionMessage(), $constraint['rule']));
            }
        }

        if ($this->hasErrors()) {
            throw new ValidationFailedException($this->errors);
        }
    }

    /**
     * @return string
     */
    private function getExceptionMessage()
    {
        return 'Can not find the validation rule with the name "%s".';
    }

    /**
     * @return bool
     */
    protected function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * @param string $validationRule
     * @param mixed  $field
     *
     * @return bool
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     */
    public function is($validationRule, $field)
    {
        if (isset($this->validationRules[$validationRule])) {
            return $this->validationRules[$validationRule]->isValid($field);
        }

        throw new ValidationRuleNotFoundException(\sprintf($this->getExceptionMessage(), $validationRule));
    }
}
