<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View\Block;

use ACP3\Core\Controller\Exception\ResultNotExistsException;
use ACP3\Core\Model\Repository\ReaderRepositoryInterface;
use ACP3\Core\View\Block\Context\FormBlockContext;

abstract class AbstractRepositoryAwareFormBlock extends AbstractFormBlock implements RepositoryAwareFormBlockInterface
{
    /**
     * @var ReaderRepositoryInterface
     */
    private $repository;
    /**
     * @var int|null
     */
    private $id;

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

        $this->id = $id;

        $data = $this->repository->getOneById($id);

        if (empty($data)) {
            throw new ResultNotExistsException();
        }

        $this->setData($data);

        return $this;
    }

    /**
     * @return int|null
     */
    protected function getId(): ?int
    {
        return $this->id;
    }
}
