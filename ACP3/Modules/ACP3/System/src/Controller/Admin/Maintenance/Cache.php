<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Maintenance;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Helpers\RedirectMessages;
use ACP3\Modules\ACP3\System;
use ACP3\Modules\ACP3\System\Exception\CacheClearException;
use ACP3\Modules\ACP3\System\Exception\InvalidCacheTypeException;

class Cache extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Core\Helpers\RedirectMessages
     */
    private $redirectMessages;
    /**
     * @var \ACP3\Modules\ACP3\System\Services\CacheClearService
     */
    private $cacheClearService;

    public function __construct(
        WidgetContext $context,
        RedirectMessages $redirectMessages,
        System\Services\CacheClearService $cacheClearService
    ) {
        parent::__construct($context);

        $this->redirectMessages = $redirectMessages;
        $this->cacheClearService = $cacheClearService;
    }

    /**
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function __invoke(?string $action = null)
    {
        if ($action !== null) {
            return $this->executePurge($action);
        }

        return [
            'cache_types' => $this->cacheClearService->getCacheTypeKeys(),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function executePurge(string $action)
    {
        $result = false;

        try {
            $this->cacheClearService->clearCacheByType($action);

            $result = true;
            $text = $this->translator->t('system', 'cache_type_' . $action . '_delete_success');
        } catch (InvalidCacheTypeException $exception) {
            $text = $this->translator->t('system', 'cache_type_not_found');
        } catch (CacheClearException $exception) {
            $text = $this->translator->t('system', 'cache_type_' . $action . '_delete_error');
        }

        return $this->redirectMessages->setMessage($result, $text, 'acp/system/maintenance/cache');
    }
}
