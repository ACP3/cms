<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation;

use ACP3\Core\Validation\Event\FormValidationEvent;
use ACP3\Core\Validation\Exceptions\ValidationFailedException;
use ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException;
use Psr\Container\ContainerInterface;
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
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * Validator constructor.
     *
     * @param EventDispatcherInterface          $eventDispatcher
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, ContainerInterface $container)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->container = $container;
    }

    /**
     * @param string $validationRule
     * @param array  $params
     *
     * @return $this
     */
    public function addConstraint(string $validationRule, array $params = [])
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
    public function addError(string $message, $field = '')
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
     * @param array  $formData
     * @param array  $extra
     */
    public function dispatchValidationEvent(string $eventName, array $formData, array $extra = [])
    {
        $this->eventDispatcher->dispatch(new FormValidationEvent($this, $formData, $extra), $eventName);
    }

    /**
     * Validates a form.
     *
     * @throws ValidationFailedException
     * @throws ValidationRuleNotFoundException
     */
    public function validate()
    {
        $this->errors = [];

        foreach ($this->constraints as $constraint) {
            if ($this->container->has($constraint['rule'])) {
                $validationRule = $this->container->get($constraint['rule']);
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
     *
     * @throws \ACP3\Core\Validation\Exceptions\ValidationRuleNotFoundException
     */
    public function is(string $validationRule, $field)
    {
        if ($this->container->has($validationRule)) {
            return $this->container->get($validationRule)->isValid($field);
        }

        throw new ValidationRuleNotFoundException(\sprintf($this->getExceptionMessage(), $validationRule));
    }
}
