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
     * @var Core\Request
     */
    private $request;
    /**
     * @var Core\Redirect
     */
    private $redirect;
    /**
     * @var Core\View
     */
    private $view;

    public function __construct(
        Core\Redirect $redirect,
        Core\Request $request,
        Core\View $view
    )
    {
        $this->redirect = $redirect;
        $this->request = $request;
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
        if (isset($_SESSION['redirect_message']) && is_array($_SESSION['redirect_message'])) {
            $this->view->assign('redirect', $_SESSION['redirect_message']);

            unset($_SESSION['redirect_message']);

            return $this->view->fetchTemplate('system/redirect_message.tpl');
        }

        return '';
    }

    /**
     * Sets a redirect messages and redirects to the given internal path
     *
     * @param $success
     * @param $text
     * @param $path
     */
    public function setMessage($success, $text, $path)
    {
        if (empty($text) === false && empty($path) === false) {
            $_SESSION['redirect_message'] = array(
                'success' => is_int($success) ? true : (bool)$success,
                'text' => $text
            );

            $this->redirect->temporary($path);
        }
    }
} 