<?php
namespace ACP3\Modules\Search;

use ACP3\Core;


class Validator extends Core\Validator\AbstractValidator
{

    /**
     * @param array $formData
     *
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (strlen($formData['search_term']) < 3) {
            $errors['search-term'] = $this->lang->t('search', 'search_term_to_short');
        }
        if (empty($formData['mods'])) {
            $errors[] = $this->lang->t('search', 'no_module_selected');
        }
        if (empty($formData['area'])) {
            $errors[] = $this->lang->t('search', 'no_area_selected');
        }
        if (empty($formData['sort']) || $formData['sort'] != 'asc' && $formData['sort'] != 'desc') {
            $errors[] = $this->lang->t('search', 'no_sorting_selected');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed($errors);
        }
    }

}
