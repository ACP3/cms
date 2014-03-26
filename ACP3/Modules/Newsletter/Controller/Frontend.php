<?php

namespace ACP3\Modules\Newsletter\Controller;

use ACP3\Core;
use ACP3\Modules\Newsletter;

/**
 * Description of NewsletterFrontend
 *
 * @author Tino Goratsch
 */
class Frontend extends Core\Modules\Controller
{

    /**
     *
     * @var Newsletter\Model
     */
    protected $model;

    protected function _init()
    {
        $this->model = new Newsletter\Model($this->db, $this->lang);
    }

    public function actionActivate()
    {
        if (Core\Validate::email($this->uri->mail) && Core\Validate::isMD5($this->uri->hash)) {
            $mail = $this->uri->mail;
            $hash = $this->uri->hash;
        } else {
            $this->uri->redirect('errors/404');
            return;
        }

        if ($this->model->accountExists($mail, $hash) === false) {
            $errors[] = $this->lang->t('newsletter', 'account_not_exists');
        }

        if (isset($errors) === true) {
            $this->view->setContent(Core\Functions::errorBox($errors));
        } else {
            $bool = $this->model->update(array('hash' => ''), array('mail' => $mail, 'hash' => $hash), Newsletter\Model::TABLE_NAME_ACCOUNTS);

            $this->view->setContent(Core\Functions::confirmBox($this->lang->t('newsletter', $bool !== false ? 'activate_success' : 'activate_error'), ROOT_DIR));
        }
    }

    public function actionDetails()
    {
        $newsletter = $this->model->getOneById((int)$this->uri->id, 1);

        if (!empty($newsletter)) {
            $this->breadcrumb->append($this->lang->t('newsletter', 'list'), $this->uri->route('newsletter/list'))
                ->append($this->lang->t('newsletter', 'list_archive'), $this->uri->route('newsletter/list_archive'))
                ->append($newsletter['title']);

            $newsletter['date_formatted'] = $this->date->format($newsletter['date'], 'short');
            $newsletter['date_iso'] = $this->date->format($newsletter['date'], 'c');
            $newsletter['text'] = Core\Functions::nl2p($newsletter['text']);

            $this->view->assign('newsletter', $newsletter);
        } else {
            $this->uri->redirect('errors/404');
        }
    }

    public function actionList()
    {
        if (isset($_POST['submit']) === true) {
            try {
                switch ($this->uri->action) {
                    case 'subscribe':
                        $this->model->validateSubscribe($_POST, $this->lang);

                        $bool = Newsletter\Helpers::subscribeToNewsletter($_POST['mail']);

                        $this->session->unsetFormToken();

                        $this->view->setContent(Core\Functions::confirmBox($this->lang->t('newsletter', $bool !== false ? 'subscribe_success' : 'subscribe_error'), ROOT_DIR));
                        return;
                        break;
                    case 'unsubscribe':
                        $this->model->validateUnsubscribe($_POST, $this->lang);

                        $bool = $this->model->delete($_POST['mail'], 'mail', Newsletter\Model::TABLE_NAME_ACCOUNTS);

                        $this->session->unsetFormToken();

                        $this->view->setContent(Core\Functions::confirmBox($this->lang->t('newsletter', $bool !== false ? 'unsubscribe_success' : 'unsubscribe_error'), ROOT_DIR));
                        return;
                        break;
                    default:
                        $this->uri->redirect('errors/404');
                }
            } catch (Core\Exceptions\InvalidFormToken $e) {
                $this->view->setContent(Core\Functions::errorBox($e->getMessage()));
            } catch (Core\Exceptions\ValidationFailed $e) {
                $this->view->assign('error_msg', $e->getMessage());
            }
        }

        $this->view->assign('form', isset($_POST['submit']) ? $_POST : array('mail' => ''));

        $field_value = $this->uri->action ? $this->uri->action : 'subscribe';

        $actions_Lang = array(
            $this->lang->t('newsletter', 'subscribe'),
            $this->lang->t('newsletter', 'unsubscribe')
        );
        $this->view->assign('actions', Core\Functions::selectGenerator('action', array('subscribe', 'unsubscribe'), $actions_Lang, $field_value, 'checked'));

        if (Core\Modules::hasPermission('captcha', 'image') === true) {
            $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha());
        }

        $this->session->generateFormToken();
    }

    public function actionListArchive()
    {
        $this->breadcrumb->append($this->lang->t('newsletter', 'list'), $this->uri->route('newsletter/list'))
            ->append($this->lang->t('newsletter', 'list_archive'));

        $newsletters = $this->model->getAll(1, POS, $this->auth->entries);
        $c_newsletters = count($newsletters);

        if ($c_newsletters > 0) {
            $pagination = new Core\Pagination(
                $this->auth,
                $this->breadcrumb,
                $this->lang,
                $this->seo,
                $this->uri,
                $this->view,
                $this->model->countAll(1)
            );
            $pagination->display();

            for ($i = 0; $i < $c_newsletters; ++$i) {
                $newsletters[$i]['date_formatted'] = $this->date->format($newsletters[$i]['date'], 'short');
            }
            $this->view->assign('newsletters', $newsletters);
        }
    }

    public function actionSidebar()
    {
        if (Core\Modules::hasPermission('captcha', 'image') === true) {
            $this->view->assign('captcha', \ACP3\Modules\Captcha\Helpers::captcha(3, 'captcha', true, 'newsletter'));
        }

        $this->session->generateFormToken('newsletter/list');

        $this->view->displayTemplate('newsletter/sidebar.tpl');
    }

}