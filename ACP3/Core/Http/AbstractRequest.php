<?php
namespace ACP3\Core\Http;

use ACP3\Core\Http\Request\UserAgent;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class AbstractRequest
 * @package ACP3\Core\Http
 */
abstract class AbstractRequest implements RequestInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $symfonyRequest;

    /**
     * @var string
     */
    protected $homepage = '';
    /**
     * @var \ACP3\Core\Http\Request\UserAgent
     */
    protected $userAgent;

    /**
     * AbstractRequest constructor.
     * @param SymfonyRequest $symfonyRequest
     */
    public function __construct(SymfonyRequest $symfonyRequest)
    {
        $this->symfonyRequest = $symfonyRequest;

        $this->fillParameterBags();
    }

    /**
     * @return SymfonyRequest
     */
    public function getSymfonyRequest()
    {
        return $this->symfonyRequest;
    }

    /**
     * Returns the used protocol of the current request
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->symfonyRequest->getScheme();
    }

    /**
     * Returns the hostname of the current request
     *
     * @return string
     */
    public function getHost()
    {
        return $this->symfonyRequest->getHost();
    }

    /**
     * Returns the protocol with the hostname
     *
     * @return string
     */
    public function getHttpHost()
    {
        return $this->symfonyRequest->getHttpHost();
    }

    /**
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        return $this->symfonyRequest->isXmlHttpRequest();
    }

    /**
     * Returns the parameter bag of the $_COOKIE superglobal
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getCookies()
    {
        return $this->symfonyRequest->cookies;
    }

    /**
     * Returns the parameter bag of the uploaded files ($_FILES superglobal)
     *
     * @return \Symfony\Component\HttpFoundation\FileBag
     */
    public function getFiles()
    {
        return $this->symfonyRequest->files;
    }

    /**
     * Returns the parameter bag of the $_POST superglobal
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getPost()
    {
        return $this->symfonyRequest->request;
    }

    /**
     * Returns the parameter bag of the $_SERVER superglobal
     *
     * @return \Symfony\Component\HttpFoundation\ServerBag
     */
    public function getServer()
    {
        return $this->symfonyRequest->server;
    }

    /**
     * @return \ACP3\Core\Http\Request\UserAgent
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $homepage
     *
     * @return $this
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;

        return $this;
    }

    protected function fillParameterBags()
    {
        $this->userAgent = new UserAgent($this->symfonyRequest->server);
    }
}
