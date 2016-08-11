<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;


use ACP3\Core\Controller\AbstractAdminAction;
use ACP3\Core\Controller\Context\AdminContext;
use ACP3\Core\Helpers\StringFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;

class Suggest extends AbstractAdminAction
{
    /**
     * @var StringFormatter
     */
    protected $stringFormatter;

    /**
     * Suggest constructor.
     * @param AdminContext $context
     * @param StringFormatter $stringFormatter
     */
    public function __construct(AdminContext $context, StringFormatter $stringFormatter)
    {
        parent::__construct($context);

        $this->stringFormatter = $stringFormatter;
    }

    /**
     * @return JsonResponse
     */
    public function execute()
    {
        $response = [];
        if ($this->request->getPost()->count() > 0) {
            $formData = $this->request->getPost()->all();

            if (!empty($formData['title'])) {
                $alias = $this->stringFormatter->makeStringUrlSafe($formData['title']);

                if (!empty($formData['prefix'])) {
                    $alias = $this->stringFormatter->makeStringUrlSafe($formData['prefix']) . '/' . $alias;
                }

                $response = ['alias' => $alias];
            }
        }

        return new JsonResponse($response);
    }
}
