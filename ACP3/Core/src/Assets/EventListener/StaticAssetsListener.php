<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\EventListener;

use ACP3\Core\Assets\Libraries;
use ACP3\Core\Assets\LibrariesCache;
use ACP3\Core\Assets\Renderer\CSSRenderer;
use ACP3\Core\Assets\Renderer\JavaScriptRenderer;
use FOS\HttpCache\SymfonyCache\CacheEvent;
use FOS\HttpCache\SymfonyCache\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class StaticAssetsListener implements EventSubscriberInterface
{
    private const REGEX_PATTERN_CSS = '!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is';
    private const REGEX_PATTERN_JS = '!@@@SMARTY:JAVASCRIPTS:BEGIN@@@(.*?)@@@SMARTY:JAVASCRIPTS:END@@@!is';
    private const PLACEHOLDER_CSS = '<!-- STYLESHEETS -->';
    private const PLACEHOLDER_JS = '<!-- JAVASCRIPTS -->';

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;
    /**
     * @var \ACP3\Core\Assets\LibrariesCache
     */
    private $librariesCache;
    /**
     * @var \ACP3\Core\Assets\Libraries
     */
    private $libraries;

    /**
     * @var Request[]
     */
    private $tracedRequests = [];
    /**
     * @var \ACP3\Core\Assets\Renderer\CSSRenderer
     */
    private $cssRenderer;
    /**
     * @var \ACP3\Core\Assets\Renderer\JavaScriptRenderer
     */
    private $javaScriptRenderer;

    public function __construct(
        CSSRenderer $cssRenderer,
        JavaScriptRenderer $javaScriptRenderer,
        RequestStack $requestStack,
        Libraries $libraries,
        LibrariesCache $librariesCache
    ) {
        $this->requestStack = $requestStack;
        $this->libraries = $libraries;
        $this->librariesCache = $librariesCache;
        $this->cssRenderer = $cssRenderer;
        $this->javaScriptRenderer = $javaScriptRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::POST_HANDLE => 'postHandle',
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }

    public function postHandle(CacheEvent $event): void
    {
        $this->tracedRequests[] = $event->getRequest();

        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $this->enableLibraries($event);

        $this->moveCssBlocksToHead($event);
        $this->moveJavaScriptBlocksToBodyEnd($event);
    }

    /**
     * Event subscriber for saving the libraries request cache.
     * Postponing saving the cache through the kernel terminate event improves the perceived performance for the user,
     * as the response has already been sent.
     */
    public function onKernelTerminate(): void
    {
        foreach ($this->tracedRequests as $request) {
            $this->librariesCache->saveEnabledLibrariesByRequest($request);
        }
    }

    private function enableLibraries(CacheEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response) {
            return;
        }

        if (!$response->getContent()) {
            return;
        }

        $libraries = [];
        foreach ($this->tracedRequests as $request) {
            $libraries = \array_merge($libraries, $this->librariesCache->getEnabledLibrariesByRequest($request));
        }

        $this->libraries->enableLibraries(\array_unique($libraries));
    }

    private function moveCssBlocksToHead(CacheEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response) {
            return;
        }

        $content = $response->getContent();

        if (\strpos($content, self::PLACEHOLDER_CSS) !== false) {
            $this->requestStack->push($event->getRequest());

            $content = \str_replace(
                self::PLACEHOLDER_CSS,
                $this->addCssLibraries() . $this->addElementsFromTemplates($content, self::REGEX_PATTERN_CSS),
                $this->getCleanedUpTemplateOutput($content, self::REGEX_PATTERN_CSS)
            );

            $response->setContent($content);
            $response->headers->set('Content-Length', (string) \strlen($content));

            $this->requestStack->pop();
        }
    }

    private function moveJavaScriptBlocksToBodyEnd(CacheEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response) {
            return;
        }

        $content = $response->getContent();

        if (\strpos($content, self::PLACEHOLDER_JS) !== false) {
            $this->requestStack->push($event->getRequest());

            $content = \str_replace(
                self::PLACEHOLDER_JS,
                $this->addJavaScriptLibraries($event->getRequest()) . $this->addElementsFromTemplates($content, self::REGEX_PATTERN_JS),
                $this->getCleanedUpTemplateOutput($content, self::REGEX_PATTERN_JS)
            );

            $response->setContent($content);
            $response->headers->set('Content-Length', (string) \strlen($content));

            $this->requestStack->pop();
        }
    }

    private function getCleanedUpTemplateOutput(string $tplOutput, string $regexPattern): string
    {
        return \preg_replace($regexPattern, '', $tplOutput);
    }

    private function addElementsFromTemplates(string $tplOutput, string $regexPattern): string
    {
        $matches = [];
        \preg_match_all($regexPattern, $tplOutput, $matches);

        return \implode("\n", \array_unique($matches[1])) . "\n";
    }

    private function addCssLibraries(): string
    {
        return $this->cssRenderer->renderHtmlElement();
    }

    private function addJavaScriptLibraries(Request $request): string
    {
        if ($request->isXmlHttpRequest() === true) {
            return '';
        }

        return $this->javaScriptRenderer->renderHtmlElement();
    }
}
