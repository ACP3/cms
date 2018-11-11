<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Share\Helpers\ShareFormFields;
use ACP3\Modules\ACP3\Share\Model\ShareModel;
use ACP3\Modules\ACP3\Share\Validation\AdminFormValidation;

class Edit extends Core\Controller\AbstractFormAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Share\Validation\AdminFormValidation
     */
    protected $adminFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\ShareFormFields
     */
    private $shareFormFields;
    /**
     * @var \ACP3\Modules\ACP3\Share\Model\ShareModel
     */
    private $shareModel;

    public function __construct(
        Core\Controller\Context\FormContext $context,
        Core\Helpers\FormToken $formTokenHelper,
        ShareFormFields $shareFormFields,
        ShareModel $shareModel,
        AdminFormValidation $adminFormValidation
    ) {
        parent::__construct($context);

        $this->formTokenHelper = $formTokenHelper;
        $this->adminFormValidation = $adminFormValidation;
        $this->shareFormFields = $shareFormFields;
        $this->shareModel = $shareModel;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws \ACP3\Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id)
    {
        $sharingInfo = $this->shareModel->getOneById($id);

        if (empty($sharingInfo) === false) {
            return [
                'SHARE_FORM_FIELDS' => $this->shareFormFields->formFields($sharingInfo['uri']),
                'form' => \array_merge(['uri' => $sharingInfo['uri']], $this->request->getPost()->all()),
                'form_token' => $this->formTokenHelper->renderFormToken(),
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function executePost(int $id)
    {
        return $this->actionHelper->handleSaveAction(function () use ($id) {
            $formData = $this->request->getPost()->all();

            $shareInfo = $this->shareModel->getOneById($id);

            $this->adminFormValidation
                ->setUriAlias($shareInfo['uri'])
                ->validate($formData);

            return $this->shareModel->save($formData, $id);
        });
    }
}
