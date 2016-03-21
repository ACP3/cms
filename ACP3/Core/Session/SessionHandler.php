<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Session;

use ACP3\Core\Database\Connection;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;

/**
 * @package ACP3\Core
 */
class SessionHandler extends AbstractSessionHandler
{
    /**
     * @var \ACP3\Core\Database\Connection
     */
    protected $db;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var bool
     */
    protected $gcCalled = false;

    /**
     * @param \ACP3\Core\Database\Connection         $db
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Http\RequestInterface       $request
     */
    public function __construct(
        Connection $db,
        ApplicationPath $appPath,
        RequestInterface $request
    ) {
        $this->db = $db;
        $this->appPath = $appPath;
        $this->request = $request;

        $this->configureSession();
    }

    /**
     * @inheritdoc
     */
    protected function startSession()
    {
        // Set the session cookie parameters
        session_set_cookie_params(0, $this->appPath->getWebRoot());

        // Start the session
        session_start();
    }

    /**
     * Secures the current session to prevent from session fixations
     */
    public function secureSession()
    {
        session_regenerate_id(true);
        $this->resetSessionData();
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
        if ($this->gcCalled === true) {
            if ($this->expireTime === 0) {
                return false;
            }

            $this->gcCalled = false;
            $this->db->getConnection()->executeUpdate(
                "DELETE FROM `{$this->db->getPrefix()}sessions` WHERE `session_starttime` + ? < ?",
                [$this->expireTime, time()]
            );
        }

        return true;
    }

    /**
     * Resets all already stored session data
     */
    protected function resetSessionData()
    {
        $_SESSION = [];
    }

    /**
     * @inheritdoc
     */
    public function read($sessionId)
    {
        $session = $this->db->fetchColumn(
            "SELECT `session_data` FROM `{$this->db->getPrefix()}sessions` WHERE `session_id` = ?",
            [$sessionId]
        );

        return $session ?: ''; // Return an empty string, if the requested session can't be found
    }

    /**
     * @inheritdoc
     */
    public function write($sessionId, $data)
    {
        $this->db->getConnection()->executeUpdate(
            "INSERT INTO `{$this->db->getPrefix()}sessions` (`session_id`, `session_starttime`, `session_data`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `session_data` = ?",
            [$sessionId, time(), $data, $data]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function destroy($sessionId)
    {
        $this->resetSessionData();

        if ($this->request->getCookies()->has(self::SESSION_NAME)) {
            setcookie(self::SESSION_NAME, '', time() - 3600, $this->appPath->getWebRoot());
        }

        // Delete the session from the database
        $this->db->getConnection()->delete($this->db->getPrefix() . 'sessions', ['session_id' => $sessionId]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function gc($sessionLifetime)
    {
        // Delay the garbage collection to the close() method, to prevent from read/write locks
        $this->gcCalled = true;

        return true;
    }
}
