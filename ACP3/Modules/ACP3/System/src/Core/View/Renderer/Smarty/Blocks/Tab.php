<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\View\Renderer\Smarty\Blocks;

use ACP3\Core\Helpers\View\Dto\TabDto;
use ACP3\Core\View\Renderer\Smarty\Blocks\AbstractBlock;
use ACP3\Modules\ACP3\System\Helper\View\Tabset as TabsetViewHelper;

class Tab extends AbstractBlock
{
    /**
     * @var \ACP3\Modules\ACP3\System\Helper\View\Tabset
     */
    private $tabset;

    public function __construct(TabsetViewHelper $tabset)
    {
        $this->tabset = $tabset;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(array $params, ?string $content, \Smarty_Internal_Template $smarty, bool &$repeat): string
    {
        if (!$repeat) {
            if (\count($smarty->smarty->_tag_stack) > 2) {
                throw new \InvalidArgumentException('It is currently not possible to nest tabs!');
            }

            $tagStack = \array_filter($smarty->smarty->_tag_stack, static function ($tag) {
                return $tag[0] === 'tabset';
            });

            if (\count($tagStack) === 0) {
                throw new \InvalidArgumentException('The {tab} block function needs to be called from within a {tabset} block function!');
            }

            $tabSetIdentifier = \reset($tagStack)[1]['identifier'];

            $this->tabset->addTab($tabSetIdentifier, new TabDto($params['title'], $content));
        }

        return '';
    }
}
