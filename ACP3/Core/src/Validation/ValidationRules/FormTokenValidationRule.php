<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Session\SessionHandlerInterface;
use ACP3\Core\Validation\Exceptions\InvalidFormTokenException;
use ACP3\Core\Validation\Validator;

class FormTokenValidationRule extends AbstractValidationRule
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Session\SessionHandlerInterface
     */
    protected $sessionHandler;

    /**
     * FormTokenValidationRule constructor.
     */
    public function __construct(
        RequestInterface $request,
        SessionHandlerInterface $sessionHandler
    ) {
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Validator $validator, $data, $field = '', array $extra = [])
    {
        if (!$this->isValid($data, $field, $extra)) {
            throw new InvalidFormTokenException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        $tokenName = SessionHandlerInterface::XSRF_TOKEN_NAME;
        $sessionToken = $this->sessionHandler->get($tokenName, '');

        return $this->request->getPost()->get($tokenName, '') === $sessionToken;
    }
}
