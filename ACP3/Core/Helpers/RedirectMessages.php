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
     * @var \ACP3\Core\SessionHandler
     */
    private $sessionHandler;
    /**
     * @var \ACP3\Core\View
     */
    private $view;

    /**
     * @param \ACP3\Core\Redirect         $redirect
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\SessionHandler   $sessionHandler
     * @param \ACP3\Core\View             $view
     */
    public function __construct(
        Core\Redirect $redirect,
        Core\Http\RequestInterface $request,
        Core\SessionHandler $sessionHandler,
        Core\View $view
    )
    {
        $this->redirect = $redirect;
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
        $this->view = $view;
    }

    /**
     * Gets the generated redirect message from setMessage()
     *
     * @return string
     * @throws \Exception
     */
    public function getMessage()
    {
        $param = $this->sessionHandler->get('redirect_message');
        if (isset($param) && is_array($param)) {
            $this->view->assign('redirect', $param);

            $this->sessionHandler->remove('redirect_message');

            return $this->view->fetchTemplate('system/redirect_message.tpl');
        }

        return '';
    }

    /**
     * Sets a redirect messages and redirects to the given internal path
     *
     * @param int|bool    $success
     * @param string      $text
     * @param string|null $path
     */
    public function setMessage($success, $text, $path = null)
    {
        if (empty($text) === false) {
            $this->sessionHandler->set(
                'redirect_message',
                [
                    'success' => is_int($success) ? true : (bool)$success,
                    'text' => $text
                ]
            );

            // If no path has been given, guess it automatically
            if ($path === null) {
                if ($this->request->getArea() === 'admin') {
                    $path .= 'acp/';
                }

                $path .= $this->request->getModuleAndController();
            }

            $this->redirect->temporary($path);
        }
    }
}
