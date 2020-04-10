<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;
use ACP3\Modules\ACP3\System\Installer\Schema;

class WYSIWYG extends AbstractFunction
{
    /**
     * @var \ACP3\Core\WYSIWYG\WysiwygFactory
     */
    protected $wysiwygFactory;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    protected $config;

    /**
     * @param \ACP3\Core\WYSIWYG\WysiwygFactory     $wysiwygFactory
     * @param \ACP3\Core\Settings\SettingsInterface $config
     */
    public function __construct(
        Core\WYSIWYG\WysiwygFactory $wysiwygFactory,
        Core\Settings\SettingsInterface $config
    ) {
        $this->wysiwygFactory = $wysiwygFactory;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionName()
    {
        return 'wysiwyg';
    }

    /**
     * {@inheritdoc}
     *
     * @throws \SmartyException
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
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
