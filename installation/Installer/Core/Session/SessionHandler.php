<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\Session;

use ACP3\Core\Session\AbstractSessionHandler;

/**
 * Class SessionHandler
 * @package ACP3\Installer\Core
 */
class SessionHandler extends AbstractSessionHandler
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
