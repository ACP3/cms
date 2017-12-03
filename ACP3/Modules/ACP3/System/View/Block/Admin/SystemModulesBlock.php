<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\View\Block\Admin;

use ACP3\Core\Modules\Modules;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;

class SystemModulesBlock extends AbstractBlock
{
    /**
     * @var Modules
     */
    private $modules;

    /**
     * SystemModulesBlock constructor.
     * @param BlockContext $context
     * @param Modules $modules
     */
    public function __construct(BlockContext $context, Modules $modules)
    {
        parent::__construct($context);

        $this->modules = $modules;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $modules = $this->modules->getAllModulesAlphabeticallySorted();
        $installedModules = $newModules = [];

        foreach ($modules as $key => $values) {
            $values['dir'] = strtolower($values['dir']);
            if ($this->modules->isInstalled($values['dir']) === true || $values['installable'] === false) {
                $installedModules[$key] = $values;
            } else {
                $newModules[$key] = $values;
            }
        }

        return [
            'installed_modules' => $installedModules,
            'new_modules' => $newModules
        ];
    }
}
