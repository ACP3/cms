<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\EventListener;

use ACP3\Core\Assets\Minifier\CSS;
use ACP3\Core\Assets\Minifier\DeferrableCSS;
use ACP3\Core\Assets\Minifier\JavaScript;
use FOS\HttpCache\SymfonyCache\CacheEvent;
use FOS\HttpCache\SymfonyCache\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class StaticAssetsListener implements EventSubscriberInterface
{
    private const REGEX_PATTERN_CSS = '!@@@SMARTY:STYLESHEETS:BEGIN@@@(.*?)@@@SMARTY:STYLESHEETS:END@@@!is';
    private const REGEX_PATTERN_JS = '!@@@SMARTY:JAVASCRIPTS:BEGIN@@@(.*?)@@@SMARTY:JAVASCRIPTS:END@@@!is';
    private const PLACEHOLDER_CSS = '<!-- STYLESHEETS -->';
    private const PLACEHOLDER_JS = '<!-- JAVASCRIPTS -->';

    /**
     * @var \ACP3\Core\Assets\Minifier\CSS
     */
    private $cssMinifier;
    /**
     * @var \ACP3\Core\Assets\Minifier\DeferrableCSS
     */
    private $deferrableCssMinifier;
    /**
     * @var \ACP3\Core\Assets\Minifier\JavaScript
     */
    private $javaScriptMinifier;
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    public function __construct(CSS $cssMinifier, DeferrableCSS $deferrableCssMinifier, JavaScript $javaScriptMinifier, RequestStack $requestStack)
    {
        $this->cssMinifier = $cssMinifier;
        $this->deferrableCssMinifier = $deferrableCssMinifier;
        $this->javaScriptMinifier = $javaScriptMinifier;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_HANDLE => 'postHandle',
        ];
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function postHandle(CacheEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $this->moveCssBlocksToHead($event);
        $this->moveJavaScriptBlocksToBodyEnd($event);
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
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
            $response->headers->set('Content-Length', \strlen($content));

            $this->requestStack->pop();
        }
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
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
            $response->headers->set('Content-Length', \strlen($content));

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

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function addCssLibraries(): string
    {
        $deferrableCssUri = $this->deferrableCssMinifier->getURI();

        return '<link rel="stylesheet" type="text/css" href="' . $this->cssMinifier->getURI() . '">' . "\n"
            . '<link rel="stylesheet" href="' . $deferrableCssUri . '" media="print" onload="this.media=\'all\'; this.onload=null;">' . "\n"
            . '<noscript><link rel="stylesheet" href="' . $deferrableCssUri . '"></noscript>' . "\n";
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function addJavaScriptLibraries(Request $request): string
    {
        if ($request->isXmlHttpRequest() === true) {
            return '';
        }

        return "<script defer src=\"{$this->javaScriptMinifier->getURI()}\"></script>\n";
    }
}
