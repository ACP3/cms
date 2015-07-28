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
     * @var \ACP3\Core\SessionHandler
     */
    protected $sessionHandler;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    /**
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\SessionHandler        $sessionHandler
     * @param \ACP3\Core\View                  $view
     */
    public function __construct(
        Core\Http\RequestInterface $request,
        Core\SessionHandler $sessionHandler,
        Core\View $view
    )
    {
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
        $this->view = $view;
    }

    /**
     * Generiert für ein Formular ein Securitytoken
     *
     * @param string $path
     *    Optionaler ACP3 interner URI Pfad, für welchen das Token gelten soll
     */
    public function generateFormToken($path = '')
    {
        $tokenName = Core\SessionHandler::XSRF_TOKEN_NAME;
        $sessionTokens = $this->sessionHandler->get($tokenName, []);

        $path = empty($path) ? $this->request->getQuery() : $path;
        $path = $path . (!preg_match('/\/$/', $path) ? '/' : '');

        if (!isset($sessionTokens[$path])) {
            $sessionTokens[$path] = sha1(uniqid(mt_rand(), true));
            $this->sessionHandler->set($tokenName, $sessionTokens);
        }

        $this->view->assign('form_token', '<input type="hidden" name="' . $tokenName . '" value="' . $sessionTokens[$path] . '" />');
    }

    /**
     * Entfernt das Securitytoken aus der Session
     *
     * @param string $path
     * @param string $token
     */
    public function unsetFormToken($path = '', $token = '')
    {
        $path = empty($path) ? $this->request->getQuery() : $path;
        $tokenName = Core\SessionHandler::XSRF_TOKEN_NAME;
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