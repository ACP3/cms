<?php
namespace ACP3\Installer\Core;

/**
 * Class SessionHandler
 * @package ACP3\Installer\Core
 */
class SessionHandler extends \ACP3\Core\SessionHandler
{
    public function __construct()
    {
        $this->configureSession();
    }

    /**
     * @inheritdoc
     */
    protected function startSession()
    {

    }

    /**
     * @inheritdoc
     */
    public function secureSession($force = false)
    {

    }

    /**
     * @inheritdoc
     */
    public function open($savePath, $sessionId)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function read($sessionId)
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function write($sessionId, $data)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function destroy($sessionId)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function gc($sessionLifetime)
    {
        return true;
    }

}