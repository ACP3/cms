<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Filters;

use ACP3\Core\Assets;
use ACP3\Core\Http\RequestInterface;

class MoveToBottom extends AbstractMoveElementFilter
{
    public const ELEMENT_CATCHER_REGEX_PATTERN = '!@@@SMARTY:JAVASCRIPTS:BEGIN@@@(.*?)@@@SMARTY:JAVASCRIPTS:END@@@!is';
    protected const PLACEHOLDER = '<!-- JAVASCRIPTS -->';

    /**
     * @var \ACP3\Core\Assets\Minifier\JavaScript
     */
    private $minifier;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;

    public function __construct(
        Assets\Minifier\JavaScript $minifier,
        RequestInterface $request
    ) {
        $this->minifier = $minifier;
        $this->request = $request;
    }

    public function __invoke(string $tplOutput, \Smarty_Internal_Template $smarty): string
    {
        if (\strpos($tplOutput, static::PLACEHOLDER) !== false) {
            return \str_replace(
                static::PLACEHOLDER,
                $this->addElementFromMinifier() . $this->addElementsFromTemplates($tplOutput),
                $this->getCleanedUpTemplateOutput($tplOutput)
            );
        }

        return $tplOutput;
    }

    protected function addElementFromMinifier(): string
    {
        if ($this->request->isXmlHttpRequest() === true) {
            return '';
        }

        return "<script defer src=\"{$this->minifier->getURI()}\"></script>\n";
    }
}
