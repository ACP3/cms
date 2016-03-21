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
     * @param string $path
     *
     * @return string
     */
    public function renderFormToken($path = '')
    {
        $tokenName = Core\Session\SessionHandlerInterface::XSRF_TOKEN_NAME;
        $sessionTokens = $this->sessionHandler->get($tokenName, []);

        $path = empty($path) ? $this->request->getQuery() : $path;
        $path = $path . (!preg_match('/\/$/', $path) ? '/' : '');

        if (!isset($sessionTokens[$path])) {
            $sessionTokens[$path] = sha1(uniqid(mt_rand(), true));
            $this->sessionHandler->set($tokenName, $sessionTokens);
        }

        return '<input type="hidden" name="' . $tokenName . '" value="' . $sessionTokens[$path] . '" />';
    }

    /**
     * Removed the form token from the session
     *
     * @param string $path
     * @param string $token
     */
    public function unsetFormToken($path = '', $token = '')
    {
        $path = empty($path) ? $this->request->getQuery() : $path;
        $tokenName = Core\Session\SessionHandlerInterface::XSRF_TOKEN_NAME;
        if (empty($token) && $this->request->getPost()->has($tokenName)) {
            $token = $this->request->getPost()->get($tokenName, '');
        }
        if (!empty($token)) {
            $sessionTokens = $this->sessionHandler->get($tokenName, []);
            if (isset($sessionTokens[$path])) {
                unset($sessionTokens[$path]);

                $this->sessionHandler->set($tokenName, $sessionTokens);
            }
        }
    }
}
