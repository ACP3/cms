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
    const ELEMENT_CATCHER_REGEX_PATTERN = '!@@@SMARTY:JAVASCRIPTS:BEGIN@@@(.*?)@@@SMARTY:JAVASCRIPTS:END@@@!is';
    const PLACEHOLDER = '<!-- JAVASCRIPTS -->';

    /**
     * @var \ACP3\Core\Assets\Minifier\MinifierInterface
     */
    protected $minifier;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;

    /**
     * @param \ACP3\Core\Assets\Minifier\MinifierInterface $minifier
     * @param \ACP3\Core\Http\RequestInterface             $request
     */
    public function __construct(
        Assets\Minifier\MinifierInterface $minifier,
        RequestInterface $request
    ) {
        $this->minifier = $minifier;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function __invoke($tplOutput, \Smarty_Internal_Template $smarty)
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

    /**
     * @return string
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function addElementFromMinifier()
    {
        if ($this->request->isXmlHttpRequest() === true) {
            return '';
        }

        return "<script src=\"{$this->minifier->getURI()}\"></script>\n";
    }
}
