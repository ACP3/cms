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
     * @var Core\Request
     */
    protected $request;
    /**
     * @var Core\Router
     */
    protected $router;

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
