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
     * @var Request[]
     */
    private array $tracedRequests = [];

    public function __construct(private readonly CSSRenderer $cssRenderer, private readonly JavaScriptRenderer $javaScriptRenderer, private readonly RequestStack $requestStack, private readonly Libraries $libraries, private readonly LibrariesCache $librariesCache)
    {
    }

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

        if ($event->getRequestType() !== HttpKernelInterface::MAIN_REQUEST) {
            return;
        }

        $this->enableLibraries($event);

        $this->combineCssBlocks($event);
        $this->combineJavaScriptBlocks($event);
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
            $libraries = array_merge($libraries, $this->librariesCache->getEnabledLibrariesByRequest($request));
        }

        $this->libraries->enableLibraries(array_unique($libraries));
    }

    private function combineCssBlocks(CacheEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response) {
            return;
        }

        $content = $response->getContent();

        if (\is_string($content) && str_contains($content, self::PLACEHOLDER_CSS)) {
            $this->requestStack->push($event->getRequest());

            $content = str_replace(
                self::PLACEHOLDER_CSS,
                $this->addCssLibraries() . $this->addElementsFromTemplates($content, self::REGEX_PATTERN_CSS),
                $this->getCleanedUpTemplateOutput($content, self::REGEX_PATTERN_CSS)
            );

            $response->setContent($content);
            $response->headers->set('Content-Length', (string) \strlen($content));

            $this->requestStack->pop();
        }
    }

    private function combineJavaScriptBlocks(CacheEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response) {
            return;
        }

        $content = $response->getContent();

        if (\is_string($content) && str_contains($content, self::PLACEHOLDER_JS)) {
            $this->requestStack->push($event->getRequest());

            $content = str_replace(
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
        return preg_replace($regexPattern, '', $tplOutput);
    }

    private function addElementsFromTemplates(string $tplOutput, string $regexPattern): string
    {
        $matches = [];
        preg_match_all($regexPattern, $tplOutput, $matches);

        return implode("\n", array_unique($matches[1])) . "\n";
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
