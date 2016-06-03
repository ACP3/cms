<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Modules\Helper;

use ACP3\Core;
use ACP3\Core\Controller\FrontendAction;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Action
 * @package ACP3\Core\Modules\Helper
 */
class Action
{
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Core\Helpers\Alerts
     */
    protected $alerts;
    /**
     * @var \ACP3\Core\Helpers\RedirectMessages
     */
    protected $redirectMessages;

    /**
     * Action constructor.
     *
     * @param \ACP3\Core\I18n\Translator $translator
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\RouterInterface $router
     * @param \ACP3\Core\View $view
     * @param \ACP3\Core\Helpers\Alerts $alerts
     * @param \ACP3\Core\Helpers\RedirectMessages $redirectMessages
     */
    public function __construct(
        Core\I18n\Translator $translator,
        Core\Http\RequestInterface $request,
        Core\RouterInterface $router,
        Core\View $view,
        Core\Helpers\Alerts $alerts,
        Core\Helpers\RedirectMessages $redirectMessages
    ) {
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
        $this->alerts = $alerts;
        $this->redirectMessages = $redirectMessages;
    }

    /**
     * @param callable $callback
     * @param null|string $path
     *
     * @return string|array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handlePostAction(callable $callback, $path = null)
    {
        try {
            return $callback();
        } catch (Core\Validation\Exceptions\InvalidFormTokenException $e) {
            return $this->redirectMessages->setMessage(
                false,
                $this->translator->t('system', 'form_already_submitted'),
                $path
            );
        } catch (Core\Validation\Exceptions\ValidationFailedException $e) {
            return $this->renderErrorBoxOnFailedFormValidation($e);
        }
    }

    /**
     * @param \Exception $exception
     * @return array|JsonResponse
     */
    private function renderErrorBoxOnFailedFormValidation(\Exception $exception)
    {
        $errors = $this->alerts->errorBox($exception->getMessage());
        if ($this->request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => false, 'content' => $errors]);
        }

        return ['error_msg' => $errors];
    }

    /**
     * @param \ACP3\Core\Controller\FrontendAction $context
     * @param string $action
     * @param callable $callback
     * @param string|null $moduleConfirmUrl
     * @param string|null $moduleIndexUrl
     *
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|void
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function handleDeleteAction(
        FrontendAction $context,
        $action,
        callable $callback,
        $moduleConfirmUrl = null,
        $moduleIndexUrl = null
    ) {
        return $this->handleCustomDeleteAction(
            $context,
            $action,
            function ($items) use ($callback, $moduleIndexUrl) {
                $result = $callback($items);

                if (is_string($result) === false) {
                    return $this->prepareRedirectMessageAfterPost($result, 'delete', $moduleIndexUrl);
                }
            },
            $moduleConfirmUrl,
            $moduleIndexUrl
        );
    }

    /**
     * @param \ACP3\Core\Controller\FrontendAction $context
     * @param string $action
     * @param callable $callback
     * @param string|null $moduleConfirmUrl
     * @param string|null $moduleIndexUrl
     *
     * @return void|string|array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     */
    public function handleCustomDeleteAction(
        FrontendAction $context,
        $action,
        callable $callback,
        $moduleConfirmUrl = null,
        $moduleIndexUrl = null
    ) {
        list($moduleConfirmUrl, $moduleIndexUrl) = $this->generateDefaultConfirmationBoxUris($moduleConfirmUrl,
            $moduleIndexUrl);
        $result = $this->deleteItem($action, $moduleConfirmUrl, $moduleIndexUrl);

        if (is_string($result)) {
            $context->setTemplate($result);
        } elseif ($action === 'confirmed' && is_array($result)) {
            return $callback($result);
        } else {
            throw new Core\Controller\Exception\ResultNotExistsException();
        }
    }

    /**
     * @param callable $callback
     * @param null|string $path
     *
     * @return string|array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleSettingsPostAction(callable $callback, $path = null)
    {
        return $this->handlePostAction(function () use ($callback, $path) {
            $result = $callback();

            return $this->prepareRedirectMessageAfterPost($result, 'settings', $path);
        }, $path);
    }

    /**
     * @param callable $callback
     * @param null|string $path
     *
     * @return string|array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleCreatePostAction(callable $callback, $path = null)
    {
        return $this->handlePostAction(function () use ($callback, $path) {
            $result = $callback();

            return $this->prepareRedirectMessageAfterPost($result, 'create', $path);
        });
    }

    /**
     * @param callable $callback
     * @param null|string $path
     *
     * @return string|array|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleEditPostAction(callable $callback, $path = null)
    {
        return $this->handlePostAction(function () use ($callback, $path) {
            $result = $callback();

            return $this->prepareRedirectMessageAfterPost($result, 'edit', $path);
        });
    }

    /**
     * @param bool|int $result
     * @param string $localization
     * @param null|string $path
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function prepareRedirectMessageAfterPost($result, $localization, $path = null)
    {
        return $this->redirectMessages->setMessage(
            $result,
            $this->translator->t('system', $localization . ($result !== false ? '_success' : '_error')),
            $path
        );
    }

    /**
     * @param string|null $moduleConfirmUrl
     * @param string|null $moduleIndexUrl
     *
     * @return array
     */
    private function generateDefaultConfirmationBoxUris($moduleConfirmUrl, $moduleIndexUrl)
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
     * helper function for deleting a result set
     *
     * @param string $action
     * @param string|null $moduleConfirmUrl
     * @param string|null $moduleIndexUrl
     *
     * @return string|array
     */
    private function deleteItem($action, $moduleConfirmUrl = null, $moduleIndexUrl = null)
    {
        $entries = $this->prepareRequestData();

        if (empty($entries)) {
            return $this->alerts->errorBoxContent($this->translator->t('system', 'no_entries_selected'));
        } elseif (empty($entries) === false && $action !== 'confirmed') {
            if (is_array($entries) === false) {
                $entries = [$entries];
            }

            $data = [
                'action' => 'confirmed',
                'entries' => $entries
            ];

            return $this->alerts->confirmBoxPost(
                $this->prepareConfirmationBoxText($entries),
                $data,
                $this->router->route($moduleConfirmUrl),
                $this->router->route($moduleIndexUrl)
            );
        }

        return is_array($entries) ? $entries : explode('|', $entries);
    }

    /**
     * @return mixed
     */
    private function prepareRequestData()
    {
        $entries = null;
        if (is_array($this->request->getPost()->get('entries')) === true) {
            $entries = $this->request->getPost()->get('entries');
        } elseif ((bool)preg_match('/^((\d+)\|)*(\d+)$/', $this->request->getParameters()->get('entries')) === true) {
            $entries = $this->request->getParameters()->get('entries');
        }

        return $entries;
    }

    /**
     * @param array $entries
     *
     * @return string
     */
    private function prepareConfirmationBoxText(array $entries)
    {
        $entriesCount = count($entries);
        if ($entriesCount === 1) {
            return $this->translator->t('system', 'confirm_delete_single');
        }

        return $this->translator->t('system', 'confirm_delete_multiple', ['{items}' => $entriesCount]);
    }
}
