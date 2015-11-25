<?php
namespace ACP3\Modules\ACP3\Captcha\Validator\ValidationRules;

use ACP3\Core\ACL;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Router;
use ACP3\Core\SessionHandler;
use ACP3\Core\User;
use ACP3\Core\Validator\ValidationRules\AbstractValidationRule;

/**
 * Class CaptchaValidationRule
 * @package ACP3\Modules\ACP3\Captcha\Validator\ValidationRules
 */
class CaptchaValidationRule extends AbstractValidationRule
{
    const NAME = 'captcha';

    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\Http\RequestInterface
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
     * @var \ACP3\Core\User
     */
    protected $user;

    /**
     * CaptchaValidationRule constructor.
     *
     * @param \ACP3\Core\ACL                   $acl
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\Router                $router
     * @param \ACP3\Core\User                  $user
     * @param \ACP3\Core\SessionHandler        $sessionHandler
     */
    public function __construct(
        ACL $acl,
        RequestInterface $request,
        Router $router,
        SessionHandler $sessionHandler,
        User $user
    )
    {
        $this->acl = $acl;
        $this->request = $request;
        $this->router = $router;
        $this->sessionHandler = $sessionHandler;
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (is_array($data) && array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        if ($this->acl->hasPermission('frontend/captcha/index/image') === true &&
            $this->user->isAuthenticated() === false
        ) {
            return $this->checkCaptcha($data, isset($extra['path']) ? $extra['path'] : '');
        }

        return true;
    }

    protected function checkCaptcha($input, $path)
    {
        $index = 'captcha_' . sha1($this->router->route(empty($path) === true ? $this->request->getQuery() : $path));

        return preg_match('/^[a-zA-Z0-9]+$/', $input) && strtolower($input) === strtolower($this->sessionHandler->get($index, ''));
    }
}