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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Cache extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        WidgetContext $context,
        private RedirectMessages $redirectMessages,
        private System\Services\CacheClearService $cacheClearService
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|RedirectResponse
     */
    public function __invoke(?string $action = null): array|RedirectResponse
    {
        if ($action !== null) {
            return $this->executePurge($action);
        }

        return [
            'cache_types' => $this->cacheClearService->getCacheTypeKeys(),
        ];
    }

    private function executePurge(string $action): JsonResponse|RedirectResponse
    {
        $result = false;

        try {
            $this->cacheClearService->clearCacheByType($action);

            $result = true;
            $text = $this->translator->t('system', 'cache_type_' . $action . '_delete_success');
        } catch (InvalidCacheTypeException) {
            $text = $this->translator->t('system', 'cache_type_not_found');
        } catch (CacheClearException) {
            $text = $this->translator->t('system', 'cache_type_' . $action . '_delete_error');
        }

        return $this->redirectMessages->setMessage($result, $text, 'acp/system/maintenance/cache');
    }
}
