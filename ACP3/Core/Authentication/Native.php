<?php
namespace ACP3\Core\Authentication;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\SessionHandler;
use ACP3\Core\User;
use ACP3\Modules\ACP3\Users\Model;

/**
 * Class Native
 * @package ACP3\Core\Authentication
 */
class Native implements AuthenticationInterface
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\SessionHandler
     */
    protected $sessionHandler;
    /**
     * @var \ACP3\Modules\ACP3\Users\Model
     */
    protected $usersModel;

    /**
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\SessionHandler        $sessionHandler
     * @param \ACP3\Modules\ACP3\Users\Model   $usersModel
     */
    public function __construct(
        RequestInterface $request,
        SessionHandler $sessionHandler,
        Model $usersModel)
    {
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
        $this->usersModel = $usersModel;
    }

    /**
     * @return int
     */
    public function authenticate()
    {
        if ($this->sessionHandler->has(User::AUTH_NAME)) {
            return $this->sessionHandler->get(User::AUTH_NAME, [])['id'];
        } elseif ($this->request->getCookies()->has(User::AUTH_NAME)) {
            list($userId, $token) = explode('|', $this->request->getCookies()->get(User::AUTH_NAME, ''));

            return !$this->verifyCredentials($userId, $token) ? $userId : -1;
        }

        return 0;
    }

    /**
     * @param int    $userId
     * @param string $token
     *
     * @return bool
     */
    protected function verifyCredentials($userId, $token)
    {
        $user = $this->usersModel->getOneById($userId);
        if (!empty($user) && $user['remember_me_token'] === $token) {
            return true;
        }

        return false;
    }
}