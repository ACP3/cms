<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\View\Renderer\Smarty\Blocks;

use ACP3\Core\View\Renderer\Smarty\Blocks\AbstractBlock;
use ACP3\Modules\ACP3\System\Helper\View\Tabset as TabsetViewHelper;

class Tabset extends AbstractBlock
{
    public function __construct(private TabsetViewHelper $tabset)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @throws \SmartyException
     */
    public function __invoke(array $params, ?string $content, \Smarty_Internal_Template $smarty, bool &$repeat): string
    {
        if ($repeat) {
            if (empty($params['identifier'])) {
                throw new \InvalidArgumentException('The {tabset} block function needs to be called with the argument "identifier"!');
            }

            $this->tabset->addTabset($params['identifier']);

            return '';
        }

        $smarty->assign('tabset', $this->tabset->getTabset($params['identifier']));
        $smarty->assign('tabset_appendix', $content);

        return $smarty->fetch('asset:System/Partials/tabset.tpl');
    }
}
