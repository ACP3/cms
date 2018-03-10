<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Widget\Index;


use ACP3\Core\Cache\CacheResponseTrait;
use ACP3\Core\Controller\AbstractWidgetAction;

class Index extends AbstractWidgetAction
{
    use CacheResponseTrait;

    public function execute(string $path, string $template = ''): array
    {
        $this->setCacheResponseCacheable(3600);

        $this->setTemplate($template);

        return [
            'path' => urldecode($path)
        ];
    }
}
