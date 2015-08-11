<?php
namespace ACP3\Core\Http;

use ACP3\Core\Http\Request\CookiesParameterBag;
use ACP3\Core\Http\Request\FilesParameterBag;
use ACP3\Core\Http\Request\ParameterBag;
use ACP3\Core\Http\Request\UserAgent;

/**
 * Class AbstractRequest
 * @package ACP3\Core\Http
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
     * @var \ACP3\Core\Http\Request\FilesParameterBag
     */
    protected $files;
    /**
     * @var \ACP3\Core\Http\Request\ParameterBag
     */
    protected $post;
    /**
     * @var \ACP3\Core\Http\Request\ParameterBag
     */
    protected $server;
    /**
     * @var \ACP3\Core\Http\Request\CookiesParameterBag
     */
    protected $cookies;
    /**
     * @var \ACP3\Core\Http\Request\UserAgent
     */
    protected $userAgent;

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
     * Returns the protocol with the hostname
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->protocol . $this->hostname;
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        if ($this->isAjax === null) {
            $this->isAjax = !empty($this->server->get('HTTP_X_REQUESTED_WITH')) && strtolower($this->server->get('HTTP_X_REQUESTED_WITH', '')) == 'xmlhttprequest';
        }

        return $this->isAjax;
    }

    /**
     * Returns the parameter bag of the $_COOKIE superglobal
     *
     * @return \ACP3\Core\Http\Request\CookiesParameterBag
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Returns the parameter bag of the uploaded files ($_FILES superglobal)
     *
     * @return \ACP3\Core\Http\Request\FilesParameterBag
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Returns the parameter bag of the $_POST superglobal
     *
     * @return \ACP3\Core\Http\Request\ParameterBag
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Returns the parameter bag of the $_SERVER superglobal
     *
     * @return \ACP3\Core\Http\Request\ParameterBag
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @return \ACP3\Core\Http\Request\UserAgent
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param array $server
     * @param array $post
     * @param array $files
     * @param array $cookies
     */
    protected function fillParameterBags(array $server, array $post, array $files, array $cookies)
    {
        $this->files = new FilesParameterBag($files);
        $this->post = new ParameterBag($post);
        $this->server = new ParameterBag($server);
        $this->cookies = new CookiesParameterBag($cookies);
        $this->userAgent = new UserAgent($this->server);
    }
}