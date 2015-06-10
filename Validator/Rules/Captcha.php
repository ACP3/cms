<?php
namespace ACP3\Core\Validator\Rules;

use ACP3\Core;

/**
 * Class Captcha
 * @package ACP3\Core\Validator\Rules
 */
class Captcha
{
    /**
     * @var \ACP3\Core\Request
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;

    /**
     * @param \ACP3\Core\Request $request
     * @param \ACP3\Core\Router  $router
     */
    public function __construct(
        Core\Request $request,
        Core\Router $router
    ) {
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * Überpürft, ob das eingegebene Captcha mit dem generierten übereinstimmt
     *
     * @param string $input
     * @param string $path
     *
     * @return boolean
     */
    public function captcha($input, $path = '')
    {
        $index = 'captcha_' . sha1($this->router->route(empty($path) === true ? $this->request->query : $path));

        return preg_match('/^[a-zA-Z0-9]+$/', $input) && isset($_SESSION[$index]) && strtolower($input) === strtolower($_SESSION[$index]) ? true : false;
    }
}
