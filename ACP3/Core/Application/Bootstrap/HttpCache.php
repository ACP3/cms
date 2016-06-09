<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Application\Bootstrap;


use ACP3\Core\View\Renderer\Smarty\Filters\MoveToBottom;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class HttpCache
 * @package ACP3\Core\Application\Bootstrap
 */
class HttpCache extends \Symfony\Component\HttpKernel\HttpCache\HttpCache
{
    const JAVASCRIPTS_REGEX_PATTERN = MoveToBottom::ELEMENT_CATCHER_REGEX_PATTERN;
    const PLACEHOLDER = '</body>';

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
