<?php
namespace ACP3\Core\Validation\ValidationRules;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Session\SessionHandlerInterface;
use ACP3\Core\Validation\Exceptions\InvalidFormTokenException;
use ACP3\Core\Validation\Validator;

/**
 * Class FormTokenValidationRule
 * @package ACP3\Core\Validation\ValidationRules
 */
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
     *
     * @param \ACP3\Core\Http\RequestInterface           $request
     * @param \ACP3\Core\Session\SessionHandlerInterface $sessionHandler
     */
    public function __construct(
        RequestInterface $request,
        SessionHandlerInterface $sessionHandler
    ) {
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @inheritdoc
     */
    public function validate(Validator $validator, $data, $field = '', array $extra = [])
    {
        if (!$this->isValid($data, $field, $extra)) {
            throw new InvalidFormTokenException();
        }
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        $tokenName = SessionHandlerInterface::XSRF_TOKEN_NAME;
        $urlQueryString = $this->request->getQuery();
        $sessionToken = $this->sessionHandler->get($tokenName);

        return (isset($sessionToken[$urlQueryString]) && $this->request->getPost()->get($tokenName, '') === $sessionToken[$urlQueryString]);
    }
}
