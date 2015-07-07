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
     * @var \ACP3\Core\RequestInterface
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
     * @param \ACP3\Core\RequestInterface $request
     * @param \ACP3\Core\Router           $router
     * @param \ACP3\Core\SessionHandler   $sessionHandler
     */
    public function __construct(
        Core\RequestInterface $request,
        Core\Router $router,
        Core\SessionHandler $sessionHandler
    ) {
        $this->request = $request;
        $this->router = $router;
        $this->sessionHandler = $sessionHandler;
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
        $index = 'captcha_' . sha1($this->router->route(empty($path) === true ? $this->request->getQuery() : $path));

        return preg_match('/^[a-zA-Z0-9]+$/', $input) && strtolower($input) === strtolower($this->sessionHandler->get($index, '')) ? true : false;
    }
}
