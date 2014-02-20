<?php
/**
 * Created by PhpStorm.
 * User: goratsch
 * Date: 22.12.13
 * Time: 17:00
 */

namespace ACP3\Modules\Search;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author Tino Goratsch
 */
class Model extends Core\Model
{

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Lang $lang)
    {
        parent::__construct($db, $lang);
    }

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
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }

}
