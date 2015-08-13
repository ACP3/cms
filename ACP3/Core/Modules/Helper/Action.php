<?php
namespace ACP3\Core\Modules\Helper;

use ACP3\Core;
use ACP3\Core\Modules\FrontendController;

/**
 * Class Action
 * @package ACP3\Core\Modules\Helper
 */
class Action
{
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
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
     * @param \ACP3\Core\Lang                     $lang
     * @param \ACP3\Core\Http\RequestInterface    $request
     * @param \ACP3\Core\Router                   $router
     * @param \ACP3\Core\View                     $view
     * @param \ACP3\Core\Helpers\Alerts           $alerts
     * @param \ACP3\Core\Helpers\RedirectMessages $redirectMessages
     */
    public function __construct(
        Core\Lang $lang,
        Core\Http\RequestInterface $request,
        Core\Router $router,
        Core\View $view,
        Core\Helpers\Alerts $alerts,
        Core\Helpers\RedirectMessages $redirectMessages
    )
    {
        $this->lang = $lang;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
        $this->alerts = $alerts;
        $this->redirectMessages = $redirectMessages;
    }

    /**
     * @param callable    $callback
     * @param null|string $path
     */
    public function handlePostAction(callable $callback, $path = null)
    {
        try {
            $callback();
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages->setMessage(false, $e->getMessage(), $path);
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->alerts->errorBox($e->getMessage()));
        }
    }

    /**
     * @param \ACP3\Core\Modules\FrontendController $context
     * @param string                                $action
     * @param callable                              $callback
     * @param string|null                           $moduleConfirmUrl
     * @param string|null                           $moduleIndexUrl
     *
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function handleDeleteAction(
        FrontendController $context,
        $action,
        callable $callback,
        $moduleConfirmUrl = null,
        $moduleIndexUrl = null
    )
    {
        $this->handleCustomDeleteAction(
            $context,
            $action,
            function ($items) use ($callback, $moduleIndexUrl) {
                $result = $callback($items);

                if (is_string($result) === false) {
                    $this->setRedirectMessageAfterPost($result, 'delete', $moduleIndexUrl);
                }
            },
            $moduleConfirmUrl,
            $moduleIndexUrl
        );
    }

    /**
     * @param \ACP3\Core\Modules\FrontendController $context
     * @param string                                $action
     * @param callable                              $callback
     * @param string|null                           $moduleConfirmUrl
     * @param string|null                           $moduleIndexUrl
     *
     * @return
     * @throws \ACP3\Core\Exceptions\ResultNotExists
     */
    public function handleCustomDeleteAction(
        FrontendController $context,
        $action,
        callable $callback,
        $moduleConfirmUrl = null,
        $moduleIndexUrl = null
    )
    {
        list($moduleConfirmUrl, $moduleIndexUrl) = $this->generateDefaultConfirmationBoxUris($moduleConfirmUrl, $moduleIndexUrl);
        $result = $this->deleteItem($action, $moduleConfirmUrl, $moduleIndexUrl);

        if (is_string($result)) {
            $context->setTemplate($result);
        } elseif ($action === 'confirmed' && is_array($result)) {
            return $callback($result);
        } else {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    /**
     * @param callable    $callback
     * @param null|string $path
     */
    public function handleSettingsPostAction(callable $callback, $path = null)
    {
        $this->handlePostAction(function () use ($callback, $path) {
            $result = $callback();

            $this->setRedirectMessageAfterPost($result, 'settings', $path);
        }, $path);
    }

    /**
     * @param callable    $callback
     * @param null|string $path
     */
    public function handleCreatePostAction(callable $callback, $path = null)
    {
        $this->handlePostAction(function () use ($callback, $path) {
            $result = $callback();

            $this->setRedirectMessageAfterPost($result, 'create', $path);
        });
    }

    /**
     * @param callable    $callback
     * @param null|string $path
     */
    public function handleEditPostAction(callable $callback, $path = null)
    {
        $this->handlePostAction(function () use ($callback, $path) {
            $result = $callback();

            $this->setRedirectMessageAfterPost($result, 'edit', $path);
        });
    }

    /**
     * @param bool|int    $result
     * @param string      $localization
     * @param null|string $path
     */
    private function setRedirectMessageAfterPost($result, $localization, $path = null)
    {
        $this->redirectMessages->setMessage(
            $result,
            $this->lang->t('system', $localization . ($result !== false ? '_success' : '_error')),
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
     * Little helper function for deleting an result set
     *
     * @param string      $action
     * @param string|null $moduleConfirmUrl
     * @param string|null $moduleIndexUrl
     *
     * @return string|array
     */
    private function deleteItem($action, $moduleConfirmUrl = null, $moduleIndexUrl = null)
    {
        if (is_array($this->request->getPost()->get('entries')) === true) {
            $entries = $this->request->getPost()->get('entries');
        } elseif ((bool)preg_match('/^((\d+)\|)*(\d+)$/', $this->request->getParameters()->get('entries')) === true) {
            $entries = $this->request->getParameters()->get('entries');
        }

        if (empty($entries)) {
            return $this->alerts->errorBoxContent($this->lang->t('system', 'no_entries_selected'));
        } elseif (empty($entries) === false && $action !== 'confirmed') {
            if (is_array($entries) === false) {
                $entries = [$entries];
            }

            $data = [
                'action' => 'confirmed',
                'entries' => $entries
            ];

            return $this->alerts->confirmBoxPost(
                $this->fetchConfirmationBoxText($entries),
                $data,
                $this->router->route($moduleConfirmUrl),
                $this->router->route($moduleIndexUrl)
            );
        } else {
            return is_array($entries) ? $entries : explode('|', $entries);
        }
    }

    /**
     * @param array $entries
     *
     * @return mixed|string
     */
    private function fetchConfirmationBoxText($entries)
    {
        $entriesCount = count($entries);

        if ($entriesCount === 1) {
            return $this->lang->t('system', 'confirm_delete_single');
        }

        return str_replace('{items}', $entriesCount, $this->lang->t('system', 'confirm_delete_multiple'));
    }

}