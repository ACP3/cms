<?php

namespace ACP3\Modules\Comments\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Comments;

/**
 * Class Index
 * @package ACP3\Modules\Comments\Controller\Admin
 */
class Index extends Core\Modules\Controller\Admin
{
    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;
    /**
     * @var Comments\Model
     */
    protected $commentsModel;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Lang $lang,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo,
        Core\Modules $modules,
        Core\Validate $validate,
        Core\Session $session,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Comments\Model $commentsModel)
    {
        parent::__construct($auth, $breadcrumb, $lang, $uri, $view, $seo, $modules, $validate, $session);

        $this->date = $date;
        $this->db = $db;
        $this->commentsModel = $commentsModel;
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/comments/index/delete', 'acp/comments');

        if ($this->uri->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->commentsModel->delete($item, 'module_id');
            }

            $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        $this->redirectMessages()->getMessage();

        $comments = $this->commentsModel->getCommentsGroupedByModule();
        $c_comments = count($comments);

        if ($c_comments > 0) {
            $canDelete = $this->modules->hasPermission('admin/comments/index/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->appendContent($this->get('core.functions')->dataTable($config));
            for ($i = 0; $i < $c_comments; ++$i) {
                $comments[$i]['name'] = $this->lang->t($comments[$i]['module'], $comments[$i]['module']);
            }
            $this->view->assign('comments', $comments);
            $this->view->assign('can_delete', $canDelete);
        }
    }

    public function actionSettings()
    {
        $config = new Core\Config($this->db, 'comments');

        if (empty($_POST) === false) {
            try {
                $validator = $this->get('comments.validator');
                $validator->validateSettings($_POST);

                $data = array(
                    'dateformat' => Core\Functions::strEncode($_POST['dateformat']),
                    'emoticons' => $_POST['emoticons'],
                );
                $bool = $config->setSettings($data);

                $this->session->unsetFormToken();

                $this->redirectMessages()->setMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/comments');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->redirectMessages()->setMessage(false, $e->getMessage(), 'acp/comments');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $alerts = new Core\Helpers\Alerts($this->uri, $this->view);
                $this->view->assign('error_msg', $alerts->errorBox($e->getMessage()));
            }
        }

        $settings = $config->getSettings();

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

        // Emoticons erlauben
        if ($this->modules->isActive('emoticons') === true) {
            $lang_allowEmoticons = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('allow_emoticons', Core\Functions::selectGenerator('emoticons', array(1, 0), $lang_allowEmoticons, $settings['emoticons'], 'checked'));
        }

        $this->session->generateFormToken();
    }

}