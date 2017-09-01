<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\View\Block\Admin;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Core\XML;
use ACP3\Modules\ACP3\System\Helper\AvailableDesignsTrait;
use ACP3\Modules\ACP3\System\Installer\Schema;

class SystemDesignsBlock extends AbstractBlock
{
    use AvailableDesignsTrait;
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var XML
     */
    private $xml;

    /**
     * SystemDesignsBlock constructor.
     * @param BlockContext $context
     * @param SettingsInterface $settings
     * @param XML $xml
     */
    public function __construct(BlockContext $context, SettingsInterface $settings, XML $xml)
    {
        parent::__construct($context);

        $this->settings = $settings;
        $this->xml = $xml;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        return [
            'designs' => $this->getAvailableDesigns()
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getXml()
    {
        return $this->xml;
    }

    /**
     * @inheritdoc
     */
    protected function selectEntry(string $directory)
    {
        return $this->settings->getSettings(Schema::MODULE_NAME)['design'] === $directory ? 1 : 0;
    }
}
