<?php
namespace ACP3\Core;

use ACP3\Core\Request\FilesParameterBag;
use ACP3\Core\Request\ParameterBag;

/**
 * Class AbstractRequest
 * @package ACP3\Core
 */
abstract class AbstractRequest implements RequestInterface
{
    /**
     * @var string
     */
    protected $protocol = '';
    /**
     * @var string
     */
    protected $hostname = '';
    /**
     * @var bool
     */
    protected $isAjax;
    /**
     * @var ParameterBag
     */
    protected $files;
    /**
     * @var ParameterBag
     */
    protected $post;
    /**
     * @var ParameterBag
     */
    protected $server;
    /**
     * @var ParameterBag
     */
    protected $cookie;

    public function __construct()
    {
        $this->fillParameterBags($_SERVER, $_POST, $_FILES, $_COOKIE);
        $this->setBaseUrl();
    }

    /**
     * Sets the base url (Protocol + Hostname)
     */
    protected function setBaseUrl()
    {
        $this->protocol = empty($this->server->get('HTTPS')) || strtolower($this->server->get('HTTPS', '')) === 'off' ? 'http://' : 'https://';
        $this->hostname = htmlentities($this->server->get('HTTP_HOST'), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Returns the used protocol of the current request
     *
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Returns the hostname of the current request
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @return bool
     */
    public function getIsAjax()
    {
        if ($this->isAjax === null) {
            $this->isAjax = !empty($this->server->get('HTTP_X_REQUESTED_WITH')) && strtolower($this->server->get('HTTP_X_REQUESTED_WITH', '')) == 'xmlhttprequest';
        }

        return $this->isAjax;
    }

    /**
     * Returns the parameter bag of the $_COOKIE superglobal
     *
     * @return ParameterBag
     */
    public function getCookie()
    {
        return $this->cookie;
    }

    /**
     * Returns the parameter bag of the uploaded files ($_FILES superglobal)
     *
     * @return \ACP3\Core\Request\ParameterBag
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Returns the parameter bag of the $_POST superglobal
     *
     * @return ParameterBag
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Returns the parameter bag of the $_SERVER superglobal
     *
     * @return \ACP3\Core\Request\ParameterBag
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param array $server
     * @param array $post
     * @param array $files
     * @param array $cookie
     */
    protected function fillParameterBags(array $server, array $post, array $files, array $cookie)
    {
        $this->files = new FilesParameterBag($files);
        $this->post = new ParameterBag($post);
        $this->server = new ParameterBag($server);
        $this->cookie = new ParameterBag($cookie);
    }

}