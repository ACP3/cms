<?php
namespace ACP3\Modules\ACP3\Captcha;

use ACP3\Core;

/**
 * Class Helpers
 * @package ACP3\Modules\ACP3\Captcha
 */
class Helpers
{
    /**
     * @var \ACP3\Core\Auth
     */
    protected $auth;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var \ACP3\Core\Http\Request
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\SessionHandler
     */
    protected $sessionHandler;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    /**
     * @param \ACP3\Core\Auth           $auth
     * @param \ACP3\Core\Http\Request   $request
     * @param \ACP3\Core\Router         $router
     * @param \ACP3\Core\SessionHandler $sessionHandler
     * @param \ACP3\Core\View           $view
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     */
    public function __construct(
        Core\Auth $auth,
        Core\Http\Request $request,
        Core\Router $router,
        Core\SessionHandler $sessionHandler,
        Core\View $view,
        Core\Helpers\Secure $secureHelper
    )
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->router = $router;
        $this->sessionHandler = $sessionHandler;
        $this->view = $view;
        $this->secureHelper = $secureHelper;
    }

    /**
     * Erzeugt das Captchafeld für das Template
     *
     * @param integer $captchaLength
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
            $path = sha1($this->router->route(empty($path) === true ? $this->request->getQuery() : $path));

            $this->sessionHandler->set('captcha_' . $path, $this->secureHelper->salt($captchaLength));

            $this->view->assign('captcha', [
                'width' => $captchaLength * 25,
                'id' => $id,
                'height' => 30,
                'input_only' => $inputOnly,
                'path' => $path
            ]);
            return $this->view->fetchTemplate('captcha/captcha.tpl');
        }
        return '';
    }
}
