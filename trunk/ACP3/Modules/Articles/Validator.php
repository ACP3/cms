<?php
namespace ACP3\Modules\Articles;

use ACP3\Core;
use ACP3\Modules\Menus;

class Validator extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Modules\Menus\Model
     */
    protected $menuModel;
    /**
     * @var \ACP3\Core\URI
     */
    protected $uri;

    public function __construct(Core\Lang $lang, Menus\Model $menuModel, Core\URI $uri)
    {
        parent::__construct($lang);

        $this->menuModel = $menuModel;
        $this->uri = $uri;
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateCreate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('articles', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('articles', 'text_to_short');
        }
        if (Core\Modules::hasPermission('admin/menus/index/create_item') === true && isset($formData['create']) === true) {
            if ($formData['create'] == 1) {
                if (Core\Validate::isNumber($formData['block_id']) === false) {
                    $errors['block-id'] = $this->lang->t('menus', 'select_menu_bar');
                }
                if (!empty($formData['parent']) && Core\Validate::isNumber($formData['parent']) === false) {
                    $errors['parent'] = $this->lang->t('menus', 'select_superior_page');
                }
                if (!empty($formData['parent']) && Core\Validate::isNumber($formData['parent']) === true) {
                    // Überprüfen, ob sich die ausgewählte übergeordnete Seite im selben Block befindet
                    $parentBlock = $this->menuModel->getMenuItemBlockIdById($formData['parent']);
                    if (!empty($parentBlock) && $parentBlock != $formData['block_id']) {
                        $errors['parent'] = $this->lang->t('menus', 'superior_page_not_allowed');
                    }
                }
                if ($formData['display'] != 0 && $formData['display'] != 1) {
                    $errors[] = $this->lang->t('menus', 'select_item_visibility');
                }
            }
        }
        if (!empty($formData['alias']) &&
            (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias']) === true)
        ) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }


        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validateEdit(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (Core\Validate::date($formData['start'], $formData['end']) === false) {
            $errors[] = $this->lang->t('system', 'select_date');
        }
        if (strlen($formData['title']) < 3) {
            $errors['title'] = $this->lang->t('articles', 'title_to_short');
        }
        if (strlen($formData['text']) < 3) {
            $errors['text'] = $this->lang->t('articles', 'text_to_short');
        }
        if (!empty($formData['alias']) &&
            (Core\Validate::isUriSafe($formData['alias']) === false || Core\Validate::uriAliasExists($formData['alias'], sprintf(Helpers::URL_KEY_PATTERN, $this->uri->id)) === true)
        ) {
            $errors['alias'] = $this->lang->t('system', 'uri_alias_unallowed_characters_or_exists');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

} 