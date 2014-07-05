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
     * @var Core\URI
     */
    private $uri;
    /**
     * @var Core\View
     */
    private $view;

    public function __construct(Core\URI $uri, Core\View $view)
    {
        $this->uri = $uri;
        $this->view = $view;
    }

    /**
     * Holt sich die von setRedirectMessage() erzeugte Redirect Nachricht
     */
    public function getMessage()
    {
        if (isset($_SESSION['redirect_message']) && is_array($_SESSION['redirect_message'])) {
            $this->view->assign('redirect', $_SESSION['redirect_message']);
            $this->view->assign('redirect_message', $this->view->fetchTemplate('system/redirect_message.tpl'));
            unset($_SESSION['redirect_message']);
        }
    }

    /**
     * Setzt eine Redirect Nachricht
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
            $this->uri->redirect($path);
        }
    }
} 