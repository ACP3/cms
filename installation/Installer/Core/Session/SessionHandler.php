<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\Session;

use ACP3\Core\Session\AbstractSessionHandler;

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
    public function secureSession()
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
