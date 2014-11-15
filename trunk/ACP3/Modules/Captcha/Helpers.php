<?php
namespace ACP3\Modules\Captcha;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\Captcha
 */
class Helpers
{
    /**
     * @var Core\Auth
     */
    protected $auth;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Core\Request
     */
    protected $request;
    /**
     * @var Core\Router
     */
    protected $router;
    /**
     * @var Core\View
     */
    protected $view;

    /**
     * @param Core\Auth $auth
     * @param Core\Request $request
     * @param Core\Router $router
     * @param Core\View $view
     * @param Core\Helpers\Secure $secureHelper
     */
    public function __construct(
        Core\Auth $auth,
        Core\Request $request,
        Core\Router $router,
        Core\View $view,
        Core\Helpers\Secure $secureHelper
    )
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
        $this->secureHelper = $secureHelper;
    }

    /**
     * Erzeugt das Captchafeld für das Template
     *
     * @param integer $captchaLength
     *  Anzahl der Zeichen, welche das Captcha haben soll
     * @param string  $id
     * @param bool    $inputOnly
     * @param string  $path
     *
     * @return string
     */
    public function captcha($captchaLength = 5, $id = 'captcha', $inputOnly = false, $path = '')
    {
        // Wenn man als User angemeldet ist, Captcha nicht anzeigen
        if ($this->auth->isUser() === false) {
            $path = sha1($this->router->route(empty($path) === true ? $this->request->query : $path));

            $_SESSION['captcha_' . $path] = $this->secureHelper->salt($captchaLength);

            $captcha = [];
            $captcha['width'] = $captchaLength * 25;
            $captcha['id'] = $id;
            $captcha['height'] = 30;
            $captcha['input_only'] = $inputOnly;
            $captcha['path'] = $path;
            $this->view->assign('captcha', $captcha);
            return $this->view->fetchTemplate('captcha/captcha.tpl');
        }
        return '';
    }

}