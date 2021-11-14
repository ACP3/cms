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
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Validator
{
    /**
     * @var array
     */
    private $errors = [];
    /**
     * @var array
     */
    private $constraints = [];

    public function __construct(private EventDispatcherInterface $eventDispatcher, private ContainerInterface $container)
    {
    }

    public function addConstraint(string $validationRule, array $params = []): self
    {
        $this->constraints[] = [
            'rule' => $validationRule,
            'params' => array_merge($this->getDefaultConstraintParams(), $params),
        ];

        return $this;
    }

    private function getDefaultConstraintParams(): array
    {
        return [
            'data' => null,
            'field' => '',
            'message' => '',
            'extra' => [],
        ];
    }

    public function addError(string $message, array|string|null $field = ''): self
    {
        if (!empty($field)) {
            $fieldName = $this->mapField($field);
            $this->errors[$fieldName] = $message;
        } else {
            $this->errors[] = $message;
        }

        return $this;
    }

    private function mapField(array|string $field): string
    {
        if (\is_array($field)) {
            $field = reset($field);
        }

        return str_replace('_', '-', $field);
    }

    public function dispatchValidationEvent(string $eventName, array $formData, array $extra = []): void
    {
        $this->eventDispatcher->dispatch(new FormValidationEvent($this, $formData, $extra), $eventName);
    }

    /**
     * Validates a form.
     *
     * @throws ValidationFailedException
     * @throws ValidationRuleNotFoundException
     */
    public function validate(): void
    {
        $this->errors = [];

        foreach ($this->constraints as $constraint) {
            if (!$this->container->has($constraint['rule'])) {
                throw new ValidationRuleNotFoundException(sprintf($this->getExceptionMessage(), $constraint['rule']));
            }

            /** @var ValidationRuleInterface $validationRule */
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
        }

        if ($this->hasErrors()) {
            throw new ValidationFailedException($this->errors);
        }
    }

    private function getExceptionMessage(): string
    {
        return 'Can not find the validation rule with the name "%s".';
    }

    private function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @param mixed $field
     *
     * @throws ValidationRuleNotFoundException
     */
    public function is(string $validationRule, $field): bool
    {
        if ($this->container->has($validationRule)) {
            return $this->container->get($validationRule)->isValid($field);
        }

        throw new ValidationRuleNotFoundException(sprintf($this->getExceptionMessage(), $validationRule));
    }
}
