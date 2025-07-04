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
use ACP3\Core\Http\RequestInterface;
use FOS\HttpCache\SymfonyCache\CacheEvent;
use FOS\HttpCache\SymfonyCache\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class StaticAssetsListener implements EventSubscriberInterface
{
    public const REGEX_PATTERN_CSS = '!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is';
    public const REGEX_PATTERN_JS = '!@@@SMARTY:JAVASCRIPTS:BEGIN@@@(.*?)@@@SMARTY:JAVASCRIPTS:END@@@!is';
    public const PLACEHOLDER_CSS = '<!-- STYLESHEETS -->';
    public const PLACEHOLDER_JS = '<!-- JAVASCRIPTS -->';

    /**
     * @var Request[]
     */
    private array $tracedRequests = [];

    public function __construct(
        private readonly CSSRenderer $cssRenderer,
        private readonly JavaScriptRenderer $javaScriptRenderer,
        private readonly RequestStack $requestStack,
        private readonly RequestInterface $request,
        private readonly Libraries $libraries,
        private readonly LibrariesCache $librariesCache,
    ) {
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
        $this->combineStaticAssetsBlocks($event);
    }

    /**
     * Event subscriber for saving the library request cache.
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
            $libraries = [
                ...$libraries,
                ...$this->librariesCache->getEnabledLibrariesByRequest($request),
            ];
        }

        $this->libraries->enableLibraries(array_unique($libraries));
    }

    private function combineStaticAssetsBlocks(CacheEvent $event): void
    {
        $response = $event->getResponse();

        if (!$response) {
            return;
        }

        $content = $response->getContent();

        if (\is_string($content) && (str_contains($content, self::PLACEHOLDER_CSS) || str_contains($content, self::PLACEHOLDER_JS))) {
            $this->requestStack->push($event->getRequest());

            $this->request->setPathInfo();
            $this->request->processQuery();

            $this->javaScriptRenderer->initialize();
            $this->cssRenderer->initialize();

            // Replace only the first occurrences of the STYLESHEETS- and JAVASCRIPTS-placeholders.
            // This prevents us from duplicating the static assets, if modules loaded via `load_module()` bring their own assets with them.
            if (str_contains($content, self::PLACEHOLDER_CSS)) {
                $content = preg_replace(
                    '/' . preg_quote(self::PLACEHOLDER_CSS, '/') . '/',
                    $this->cssRenderer->renderHtmlElement() . $this->addElementsFromTemplates($content, self::REGEX_PATTERN_CSS),
                    $this->getCleanedUpTemplateOutput($content, self::REGEX_PATTERN_CSS),
                    1
                );
            }
            if (str_contains($content, self::PLACEHOLDER_JS)) {
                $content = preg_replace(
                    '/' . preg_quote(self::PLACEHOLDER_JS, '/') . '/',
                    $this->javaScriptRenderer->renderHtmlElement() . $this->addElementsFromTemplates($content, self::REGEX_PATTERN_JS),
                    $this->getCleanedUpTemplateOutput($content, self::REGEX_PATTERN_JS),
                    1
                );
            }

            // Replace all the remaining placeholders
            $content = str_replace(
                [
                    self::PLACEHOLDER_CSS,
                    self::PLACEHOLDER_JS,
                ],
                '',
                $content
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
}
