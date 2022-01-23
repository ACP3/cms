<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;
use ACP3\Core\View\Renderer\Smarty\Functions\AbstractFunction;
use ACP3\Modules\ACP3\System\Installer\Schema;

class WYSIWYG extends AbstractFunction
{
    public function __construct(private Core\WYSIWYG\WysiwygFactory $wysiwygFactory, private Core\Settings\SettingsInterface $config)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @throws \SmartyException
     */
    public function __invoke(array $params, \Smarty_Internal_Template $smarty): mixed
    {
        $params['id'] = !empty($params['id']) ? $params['id'] : $params['name'];
        $editorServiceId = $params['editor'] ?? null;

        $serviceId = $this->config->getSettings(Schema::MODULE_NAME)['wysiwyg'];
        $wysiwyg = $this->wysiwygFactory->create($editorServiceId ?? $serviceId);

        $wysiwyg->setParameters($params);
        $smarty->smarty->assign($wysiwyg->getData());

        return $smarty->smarty->fetch('asset:System/Partials/wysiwyg.tpl');
    }
}
