<?php
namespace ACP3\Core\Helpers;

use ACP3\Core;

/**
 * Class FormToken
 * @package ACP3\Core\Helpers
 */
class FormToken
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
     * FormToken constructor.
     *
     * @param \ACP3\Core\Http\RequestInterface           $request
     * @param \ACP3\Core\Session\SessionHandlerInterface $sessionHandler
     */
    public function __construct(
        Core\Http\RequestInterface $request,
        Core\Session\SessionHandlerInterface $sessionHandler
    ) {
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * Generates and renders the form token
     *
     * @return string
     */
    public function renderFormToken()
    {
        $tokenName = Core\Session\SessionHandlerInterface::XSRF_TOKEN_NAME;
        $sessionToken = $this->sessionHandler->get($tokenName);

        if (empty($sessionToken)) {
            $sessionToken = sha1(uniqid(mt_rand(), true));
            $this->sessionHandler->set($tokenName, $sessionToken);
        }

        return '<input type="hidden" name="' . $tokenName . '" value="' . $sessionToken . '" />';
    }

    /**
     * Removes the form token from the session
     *
     * @param string $token
     */
    public function unsetFormToken($token = '')
    {
        $tokenName = Core\Session\SessionHandlerInterface::XSRF_TOKEN_NAME;
        if (empty($token) && $this->request->getPost()->has($tokenName)) {
            $token = $this->request->getPost()->get($tokenName, '');
        }
        if (!empty($token)) {
            $sessionToken = $this->sessionHandler->get($tokenName);
            if (!empty($sessionToken)) {
                $this->sessionHandler->remove($tokenName);
            }
        }
    }
}
