<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Share\Event\Listener;


use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;

class AddShariffOnLayoutContentAfterListener
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\View
     */
    private $view;

    /**
     * AddShariffOnLayoutContentAfterListener constructor.
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\View                  $view
     */
    public function __construct(RequestInterface $request, View $view)
    {
        $this->request = $request;
        $this->view = $view;
    }

    public function execute(): void
    {
        if ($this->request->getArea() === AreaEnum::AREA_FRONTEND) {
            $this->view->assign('shariff', [
                'path' => $this->request->getUriWithoutPages()
            ]);

            $this->view->displayTemplate('Share/Partials/add_shariff.tpl');
        }
    }
}
