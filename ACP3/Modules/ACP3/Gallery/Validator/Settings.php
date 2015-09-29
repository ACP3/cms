<?php
namespace ACP3\Modules\ACP3\Gallery\Validator;

use ACP3\Core;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Gallery\Validator
 */
class Settings extends Core\Validator\AbstractValidator
{
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;

    /**
     * @param Core\Lang                           $lang
     * @param Core\Validator\Rules\Misc           $validate
     * @param Core\Modules                        $modules
     */
    public function __construct(
        Core\Lang $lang,
        Core\Validator\Rules\Misc $validate,
        Core\Modules $modules
    )
    {
        parent::__construct($lang, $validate);

        $this->modules = $modules;
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validateFormKey();

        $this->errors = [];
        if (empty($formData['dateformat']) || ($formData['dateformat'] !== 'long' && $formData['dateformat'] !== 'short')) {
            $this->errors['dateformat'] = $this->lang->t('system', 'select_date_format');
        }
        if ($this->validate->isNumber($formData['sidebar']) === false) {
            $this->errors['sidebar'] = $this->lang->t('system', 'select_sidebar_entries');
        }
        if (!isset($formData['overlay']) || $formData['overlay'] != 1 && $formData['overlay'] != 0) {
            $this->errors['overlay'] = $this->lang->t('gallery', 'select_use_overlay');
        }
        if ($this->modules->isActive('comments') === true && (!isset($formData['comments']) || $formData['comments'] != 1 && $formData['comments'] != 0)) {
            $this->errors['comments'] = $this->lang->t('gallery', 'select_allow_comments');
        }
        if ($this->validate->isNumber($formData['thumbwidth']) === false || $this->validate->isNumber($formData['width']) === false || $this->validate->isNumber($formData['maxwidth']) === false) {
            $this->errors[] = $this->lang->t('gallery', 'invalid_image_width_entered');
        }
        if ($this->validate->isNumber($formData['thumbheight']) === false || $this->validate->isNumber($formData['height']) === false || $this->validate->isNumber($formData['maxheight']) === false) {
            $this->errors[] = $this->lang->t('gallery', 'invalid_image_height_entered');
        }
        if ($this->validate->isNumber($formData['filesize']) === false) {
            $this->errors['filesize'] = $this->lang->t('gallery', 'invalid_image_filesize_entered');
        }

        $this->_checkForFailedValidation();
    }
}