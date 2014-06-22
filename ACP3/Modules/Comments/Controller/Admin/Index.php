<?php

namespace ACP3\Modules\Comments\Controller\Admin;

use ACP3\Core;
use ACP3\Modules\Comments;

/**
 * Description of CommentsAdmin
 *
 * @author Tino Goratsch
 */
class Index extends Core\Modules\Controller\Admin
{

    /**
     *
     * @var Comments\Model
     */
    protected $model;

    public function preDispatch()
    {
        parent::preDispatch();

        $this->model = new Comments\Model($this->db);
    }

    public function actionDelete()
    {
        $items = $this->_deleteItem('acp/comments/index/delete', 'acp/comments');

        if ($this->uri->action === 'confirmed') {
            $bool = false;
            foreach ($items as $item) {
                $bool = $this->model->delete($item, 'module_id');
            }
            Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool !== false ? 'delete_success' : 'delete_error'), 'acp/comments');
        } elseif (is_string($items)) {
            throw new Core\Exceptions\ResultNotExists();
        }
    }

    public function actionIndex()
    {
        Core\Functions::getRedirectMessage();

        $comments = $this->model->getCommentsGroupedByModule();
        $c_comments = count($comments);

        if ($c_comments > 0) {
            $canDelete = Core\Modules::hasPermission('admin/comments/index/delete');
            $config = array(
                'element' => '#acp-table',
                'sort_col' => $canDelete === true ? 1 : 0,
                'sort_dir' => 'desc',
                'hide_col_sort' => $canDelete === true ? 0 : ''
            );
            $this->appendContent(Core\Functions::dataTable($config));
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
                $validator = new Comments\Validator($this->lang, $this->auth, $this->date, $this->model);
                $validator->validateSettings($_POST);

                $data = array(
                    'dateformat' => Core\Functions::strEncode($_POST['dateformat']),
                    'emoticons' => $_POST['emoticons'],
                );
                $bool = $config->setSettings($data);

                $this->session->unsetFormToken();

                Core\Functions::setRedirectMessage($bool, $this->lang->t('system', $bool === true ? 'settings_success' : 'settings_error'), 'acp/comments');
            } catch (Core\Exceptions\InvalidFormToken $e) {
                Core\Functions::setRedirectMessage(false, $e->getMessage(), 'acp/comments');
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $settings = $config->getSettings();

        $this->view->assign('dateformat', $this->date->dateFormatDropdown($settings['dateformat']));

        // Emoticons erlauben
        if (Core\Modules::isActive('emoticons') === true) {
            $lang_allowEmoticons = array($this->lang->t('system', 'yes'), $this->lang->t('system', 'no'));
            $this->view->assign('allow_emoticons', Core\Functions::selectGenerator('emoticons', array(1, 0), $lang_allowEmoticons, $settings['emoticons'], 'checked'));
        }

        $this->session->generateFormToken();
    }

}
