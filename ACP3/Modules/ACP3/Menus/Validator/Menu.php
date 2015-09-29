<?php
namespace ACP3\Modules\ACP3\Menus\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Model\MenuRepository;

/**
 * Class Menu
 * @package ACP3\Modules\ACP3\Menus\Validator
 */
class Menu extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuRepository
     */
    protected $menuRepository;

    /**
     * @param \ACP3\Core\Lang                               $lang
     * @param \ACP3\Core\Validator\Rules\Misc               $validate
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuRepository $menuRepository
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        MenuRepository $menuRepository
    )
    {
        parent::__construct($lang, $validate);

        $this->menuRepository = $menuRepository;
    }

    /**
     * @param array $formData
     * @param int   $menuId
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $menuId = 0)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (!preg_match('/^[a-zA-Z]+\w/', $formData['index_name'])) {
            $this->errors['index-name'] = $this->lang->t('menus', 'type_in_index_name');
        }
        if (!isset($this->errors) && $this->menuRepository->menuExistsByName($formData['index_name'], $menuId) === true) {
            $this->errors['index-name'] = $this->lang->t('menus', 'index_name_unique');
        }
        if (strlen($formData['title']) < 3) {
            $this->errors['title'] = $this->lang->t('menus', 'menu_bar_title_to_short');
        }

        $this->_checkForFailedValidation();
    }

}
