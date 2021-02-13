<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Modules\Helper;

use ACP3\Core;
use Doctrine\DBAL\ConnectionException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Action
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Core\Helpers\Alerts
     */
    private $alerts;
    /**
     * @var \ACP3\Core\Helpers\RedirectMessages
     */
    private $redirectMessages;
    /**
     * @var \ACP3\Core\Database\Connection
     */
    private $db;

    public function __construct(
        Core\Database\Connection $db,
        Core\I18n\Translator $translator,
        Core\Http\RequestInterface $request,
        Core\Router\RouterInterface $router,
        Core\Helpers\Alerts $alerts,
        Core\Helpers\RedirectMessages $redirectMessages
    ) {
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;
        $this->alerts = $alerts;
        $this->redirectMessages = $redirectMessages;
        $this->db = $db;
    }

    /**
     * @return string|array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function handlePostAction(callable $callback, ?string $path = null)
    {
        try {
            $this->db->getConnection()->beginTransaction();

            $result = $callback();

            $this->db->getConnection()->commit();

            return $result;
        } catch (Core\Validation\Exceptions\InvalidFormTokenException $e) {
            $this->db->getConnection()->rollBack();

            return $this->redirectMessages->setMessage(
                false,
                $this->translator->t('system', 'form_already_submitted'),
                $path
            );
        } catch (Core\Validation\Exceptions\ValidationFailedException $e) {
            $this->db->getConnection()->rollBack();

            return $this->renderErrorBoxOnFailedFormValidation($e);
        } catch (ConnectionException $e) {
            $this->db->getConnection()->rollBack();

            throw $e;
        }
    }

    public function renderErrorBoxOnFailedFormValidation(\Exception $exception): Response
    {
        $errors = $this->alerts->errorBox($exception->getMessage());

        return new Response($errors, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @return array|JsonResponse|RedirectResponse
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function handleDeleteAction(
        ?string $action,
        callable $callback,
        ?string $moduleConfirmUrl = null,
        ?string $moduleIndexUrl = null
    ) {
        return $this->handleCustomDeleteAction(
            $action,
            function (array $items) use ($callback, $moduleIndexUrl) {
                $result = $callback($items);

                return $this->prepareRedirectMessageAfterPost($result, 'delete', $moduleIndexUrl);
            },
            $moduleConfirmUrl,
            $moduleIndexUrl
        );
    }

    /**
     * @return array|JsonResponse|RedirectResponse|Response
     */
    public function handleCustomDeleteAction(
        ?string $action,
        callable $callback,
        ?string $moduleConfirmUrl = null,
        ?string $moduleIndexUrl = null
    ) {
        [$moduleConfirmUrl, $moduleIndexUrl] = $this->generateDefaultConfirmationBoxUris(
            $moduleConfirmUrl,
            $moduleIndexUrl
        );
        $result = $this->deleteItem($action, $moduleConfirmUrl, $moduleIndexUrl);

        if ($result instanceof Response) {
            return $result;
        }

        if ($action === 'confirmed') {
            return $callback($result);
        }

        return $result;
    }

    /**
     * @return string|array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function handleDuplicateAction(callable $callback, ?string $path = null)
    {
        return $this->handlePostAction(function () use ($callback, $path) {
            $result = $callback();

            return $this->prepareRedirectMessageAfterPost($result, 'duplicate', $path);
        }, $path);
    }

    /**
     * @return string|array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function handleSettingsPostAction(callable $callback, ?string $path = null)
    {
        return $this->handlePostAction(function () use ($callback, $path) {
            $result = $callback();

            return $this->prepareRedirectMessageAfterPost($result, 'settings', $path);
        }, $path);
    }

    /**
     * @return array|string|JsonResponse|RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function handleSaveAction(callable $callback, ?string $path = null)
    {
        return $this->handlePostAction(function () use ($callback, $path) {
            $result = $callback();

            return $this->prepareRedirectMessageAfterPost($result, 'save', $path);
        });
    }

    /**
     * @param bool|int $result
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function prepareRedirectMessageAfterPost($result, string $phrase, ?string $path = null)
    {
        return $this->setRedirectMessage(
            $result,
            $this->translator->t('system', $phrase . ($result !== false ? '_success' : '_error')),
            $path
        );
    }

    /**
     * @param bool|int $result
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setRedirectMessage($result, string $translatedText, ?string $path = null)
    {
        return $this->redirectMessages->setMessage(
            $result,
            $translatedText,
            $this->request->getPost()->has('continue') ? $this->request->getPathInfo() : $path
        );
    }

    private function generateDefaultConfirmationBoxUris(?string $moduleConfirmUrl, ?string $moduleIndexUrl): array
    {
        if ($moduleConfirmUrl === null) {
            $moduleConfirmUrl = $this->request->getFullPath();
        }

        if ($moduleIndexUrl === null) {
            $moduleIndexUrl = $this->request->getModuleAndController();
        }

        return [$moduleConfirmUrl, $moduleIndexUrl];
    }

    /**
     * helper function for deleting a result set.
     *
     * @return array|Response|JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function deleteItem(?string $action, ?string $moduleConfirmUrl = null, ?string $moduleIndexUrl = null)
    {
        $entries = $this->prepareRequestData();

        if (empty($entries)) {
            return $this->redirectMessages->setMessage(
                false,
                $this->translator->t('system', 'no_entries_selected'),
                $moduleIndexUrl
            );
        }

        if ($action !== 'confirmed') {
            $data = [
                'action' => 'confirmed',
                'entries' => $entries,
            ];

            return new Response(
                $this->alerts->confirmBoxPost(
                    $this->prepareConfirmationBoxText($entries),
                    $data,
                    $this->router->route($moduleConfirmUrl),
                    $this->router->route($moduleIndexUrl)
                )
            );
        }

        return $entries;
    }

    /**
     * @return array<string|int>
     */
    private function prepareRequestData(): array
    {
        if (\is_array($this->request->getPost()->get('entries')) === true) {
            return $this->request->getPost()->get('entries');
        }

        if ((bool) \preg_match('/^((\d+)\|)*(\d+)$/', $this->request->getParameters()->get('entries')) === true) {
            return \explode('|', $this->request->getParameters()->get('entries'));
        }

        return [];
    }

    private function prepareConfirmationBoxText(array $entries): string
    {
        $entriesCount = \count($entries);
        if ($entriesCount === 1) {
            return $this->translator->t('system', 'confirm_delete_single');
        }

        return $this->translator->t('system', 'confirm_delete_multiple', ['{items}' => $entriesCount]);
    }
}
