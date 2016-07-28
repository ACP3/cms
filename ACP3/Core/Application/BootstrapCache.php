<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Application;


use ACP3\Core\Session\SessionHandlerInterface;
use ACP3\Core\View\Renderer\Smarty\Filters\MoveToBottom;
use FOS\HttpCache\SymfonyCache\EventDispatchingHttpCache;
use FOS\HttpCache\SymfonyCache\PurgeSubscriber;
use FOS\HttpCache\SymfonyCache\RefreshSubscriber;
use FOS\HttpCache\SymfonyCache\UserContextSubscriber;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Component\HttpKernel\HttpCache\SurrogateInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class BootstrapCache
 * @package ACP3\Core\Application
 */
class BootstrapCache extends EventDispatchingHttpCache
{
    const JAVASCRIPTS_REGEX_PATTERN = MoveToBottom::ELEMENT_CATCHER_REGEX_PATTERN;
    const PLACEHOLDER = '</body>';

    /**
     * @inheritdoc
     */
    public function __construct(
        HttpKernelInterface $kernel,
        StoreInterface $store,
        SurrogateInterface $surrogate = null,
        array $options = array())
    {
        parent::__construct($kernel, $store, $surrogate, $options);

        $this->addSubscriber(new UserContextSubscriber([
            'user_hash_uri' => '/widget/users/index/hash/',
            'session_name_prefix' => SessionHandlerInterface::SESSION_NAME
        ]));
        $this->addSubscriber(new PurgeSubscriber());
        $this->addSubscriber(new RefreshSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $response = parent::handle($request, $type, $catch);

        $this->moveStaticAssetsAround($response);

        return $response;
    }

    /**
     * @param Response $response
     */
    private function moveStaticAssetsAround(Response $response)
    {
        $content = $response->getContent();
        if (strpos($content, static::PLACEHOLDER) !== false) {
            $content = str_replace(
                static::PLACEHOLDER,
                $this->addElementsFromTemplates($content) . "\n" . static::PLACEHOLDER,
                $this->getCleanedUpTemplateOutput($content)
            );

            $response->setContent($content);
            $response->headers->set('Content-Length', strlen($content));
        }
    }

    /**
     * @param string $tplOutput
     * @return string
     */
    private function getCleanedUpTemplateOutput($tplOutput)
    {
        return preg_replace(static::JAVASCRIPTS_REGEX_PATTERN, '', $tplOutput);
    }

    /**
     * @param string $tplOutput
     * @return string
     */
    private function addElementsFromTemplates($tplOutput)
    {
        $matches = [];
        preg_match_all(static::JAVASCRIPTS_REGEX_PATTERN, $tplOutput, $matches);

        return implode("\n", array_unique($matches[1])) . "\n";
    }
}
