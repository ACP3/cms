<?php

namespace ACP3\Modules\Feeds;

use ACP3\Core;

/**
 * Class Validator
 * @package ACP3\Modules\Feeds
 */
class Validator extends Core\Validator\AbstractValidator
{

    /**
     * @param array $formData
     * @throws \ACP3\Core\Exceptions\ValidationFailed
     */
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
