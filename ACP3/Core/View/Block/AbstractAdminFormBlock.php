<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block;

use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Model\Repository\ReaderRepositoryInterface;
use ACP3\Core\View\Block\Context\FormBlockContext;

abstract class AbstractAdminFormBlock extends AbstractFormBlock implements AdminFormBlockInterface
{
    /**
     * @var ReaderRepositoryInterface
     */
    private $repository;

    /**
     * AbstractFormTemplate constructor.
     * @param Context\FormBlockContext $context
     * @param ReaderRepositoryInterface $repository
     */
    public function __construct(FormBlockContext $context, ReaderRepositoryInterface $repository)
    {
        parent::__construct($context);

        $this->repository = $repository;
    }

    /**
     * @param int $id
     * @return $this
     * @throws ResultNotExistsException
     */
    public function setDataById(?int $id)
    {
        if ($id === null) {
            return $this;
        }

        $data = $this->repository->getOneById($id);

        if (empty($data)) {
            throw new ResultNotExistsException();
        }

        $this->setData($data);

        return $this;
    }
}
