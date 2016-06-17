<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Maintenance;

use ACP3\Core;
use ACP3\Modules\ACP3\System;

/**
 * Class UpdateCheck
 * @package ACP3\Modules\ACP3\System\Controller\Admin\Maintenance
 */
class UpdateCheck extends Core\Controller\AbstractAdminAction
{
    public function execute()
    {
        $file = @file_get_contents('http://www.acp3-cms.net/update.txt');
        if ($file !== false) {
            $data = explode('||', $file);
            if (count($data) === 2) {
                $update = [
                    'installed_version' => Core\Application\BootstrapInterface::VERSION,
                    'current_version' => $data[0],
                ];

                if (version_compare($update['installed_version'], $update['current_version'], '>=')) {
                    $update['text'] = $this->translator->t('system', 'acp3_up_to_date');
                    $update['class'] = 'success';
                } else {
                    $update['text'] = $this->translator->t(
                        'system',
                        'acp3_not_up_to_date',
                        [
                            '%link_start%' => '<a href="' . $data[1] . '" target="_blank">',
                            '%link_end%' => '</a>'
                        ]
                    );
                    $update['class'] = 'error';
                }

                $this->view->assign('update', $update);
            }
        }
    }
}
