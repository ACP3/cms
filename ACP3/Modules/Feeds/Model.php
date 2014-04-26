<?php

namespace ACP3\Modules\Feeds;

use ACP3\Core;

/**
 * Description of Model
 *
 * @author goratsch
 */
class Model extends Core\Model
{

    public function __construct(\Doctrine\DBAL\Connection $db, Core\Lang $lang)
    {
        parent::__construct($db, $lang);
    }

    public function validateSettings(array $formData)
    {
        $this->validateFormKey();

        $errors = array();
        if (empty($formData['feed_type']) || in_array($formData['feed_type'], array('RSS 1.0', 'RSS 2.0', 'ATOM')) === false) {
            $errors['mail'] = $this->lang->t('feeds', 'select_feed_type');
        }

        if (!empty($errors)) {
            throw new Core\Exceptions\ValidationFailed(Core\Functions::errorBox($errors));
        }
    }
}
