<?php
namespace ACP3\Core\Helpers;

use ACP3\Core;

/**
 * Class RedirectMessages
 * @package ACP3\Core\Helpers
 */
class RedirectMessages
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Redirect
     */
    private $redirect;
    /**
     * @var \ACP3\Core\Session\SessionHandlerInterface
     */
    private $sessionHandler;

    /**
     * RedirectMessages constructor.
     *
     * @param \ACP3\Core\Redirect                        $redirect
     * @param \ACP3\Core\Http\RequestInterface           $request
     * @param \ACP3\Core\Session\SessionHandlerInterface $sessionHandler
     */
    public function __construct(
        Core\Redirect $redirect,
        Core\Http\RequestInterface $request,
        Core\Session\SessionHandlerInterface $sessionHandler
    ) {
        $this->redirect = $redirect;
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * Gets the generated redirect message from setMessage()
     *
     * @return array|null
     * @throws \Exception
     */
    public function getMessage()
    {
        $param = $this->sessionHandler->get('redirect_message');
        if (isset($param) && is_array($param)) {
            $this->sessionHandler->remove('redirect_message');
        }

        return $param;
    }

    /**
     * Sets a redirect messages and redirects to the given internal path
     *
     * @param int|bool    $success
     * @param string      $text
     * @param string|null $path
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setMessage($success, $text, $path = null)
    {
        $this->sessionHandler->set(
            'redirect_message',
            [
                'success' => is_int($success) ? true : (bool)$success,
                'text' => $text
            ]
        );

        // If no path has been given, guess it automatically
        if ($path === null) {
            $path = $this->request->getModuleAndController();
        }

        return $this->redirect->temporary($path);
    }
}
