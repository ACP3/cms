<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\View\Block\Context;


use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\I18n\Translator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DataGridBlockContext extends BlockContext
{
    /**
     * @var ResultsPerPage
     */
    private $resultsPerPage;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var Translator
     */
    private $translator;

    /**
     * DataGridBlockContext constructor.
     * @param BlockContext $context
     * @param Translator $translator
     * @param ResultsPerPage $resultsPerPage
     * @param ContainerInterface $container
     */
    public function __construct(
        BlockContext $context,
        Translator $translator,
        ResultsPerPage $resultsPerPage,
        ContainerInterface $container
    ) {
        parent::__construct($context->getView(), $context->getBreadcrumb(), $context->getTitle());

        $this->resultsPerPage = $resultsPerPage;
        $this->container = $container;
        $this->translator = $translator;
    }

    /**
     * @return ResultsPerPage
     */
    public function getResultsPerPage(): ResultsPerPage
    {
        return $this->resultsPerPage;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @return Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
