<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Renderer\Smarty\Functions;

use ACP3\Core;

/**
 * Class WYSIWYG
 * @package ACP3\Core\View\Renderer\Smarty\Functions
 */
class WYSIWYG extends AbstractFunction
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;
    /**
     * @var \ACP3\Core\WYSIWYG\WysiwygFactory
     */
    protected $wysiwygFactory;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;

    /**
     * @param \ACP3\Core\WYSIWYG\WysiwygFactory $wysiwygFactory
     * @param \ACP3\Core\Config                 $config
     */
    public function __construct(
        Core\WYSIWYG\WysiwygFactory $wysiwygFactory,
        Core\Config $config
    ) {
        $this->wysiwygFactory = $wysiwygFactory;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionName()
    {
        return 'wysiwyg';
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, \Smarty_Internal_Template $smarty)
    {
        $params['id'] = !empty($params['id']) ? $params['id'] : $params['name'];

        $serviceId = $this->config->getSettings('system')['wysiwyg'];
        $wysiwyg = $this->wysiwygFactory->create($serviceId);

        $wysiwyg->setParameters($params);
        $smarty->smarty->assign($wysiwyg->getData());

        return $smarty->smarty->fetch('asset:System/Partials/wysiwyg.tpl');
    }
}
