<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;


use ACP3\Core\Controller\Event\CustomTemplateVariableEvent;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OnControllerCustomTemplateVariableListener
 * @package ACP3\Modules\ACP3\Seo\Event\Listener
 */
class OnControllerCustomTemplateVariableListener extends Event
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;

    /**
     * OnCustomTemplateVariable constructor.
     *
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatements
     */
    public function __construct(MetaStatements $metaStatements)
    {
        $this->metaStatements = $metaStatements;
    }

    /**
     * @param \ACP3\Core\Controller\Event\CustomTemplateVariableEvent $event
     */
    public function onCustomTemplateVariable(CustomTemplateVariableEvent $event)
    {
        $event->getView()->assign('META', $this->metaStatements->getMetaTags());
    }
}
