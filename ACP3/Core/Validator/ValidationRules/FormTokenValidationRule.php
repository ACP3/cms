<?php
namespace ACP3\Core\Validator\ValidationRules;

use ACP3\Core\Exceptions\InvalidFormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\SessionHandler;
use ACP3\Core\Validator\Validator;

/**
 * Class FormTokenValidationRule
 * @package ACP3\Core\Validator\ValidationRules
 */
class FormTokenValidationRule extends AbstractValidationRule
{
    const NAME = 'form_token';

    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\SessionHandler
     */
    protected $sessionHandler;

    /**
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\SessionHandler        $sessionHandler
     */
    public function __construct(
        RequestInterface $request,
        SessionHandler $sessionHandler
    )
    {
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @inheritdoc
     */
    public function validate(Validator $validator, $data, $field = '', array $extra = [])
    {
        if (!$this->isValid($data, $field, $extra)) {
            throw new InvalidFormToken();
        }
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        $tokenName = SessionHandler::XSRF_TOKEN_NAME;
        $urlQueryString = $this->request->getQuery();
        $sessionToken = $this->sessionHandler->get($tokenName);

        return (isset($sessionToken[$urlQueryString]) && $this->request->getPost()->get($tokenName, '') === $sessionToken[$urlQueryString]);
    }
}