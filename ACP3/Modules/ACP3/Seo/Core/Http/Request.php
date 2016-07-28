<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core\Http;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository;

/**
 * Class Request
 * @package ACP3\Modules\ACP3\Seo\Core\Http
 */
class Request extends \ACP3\Core\Http\Request
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository
     */
    protected $seoRepository;

    /**
     * Request constructor.
     *
     * @param \Symfony\Component\HttpFoundation\Request $symfonyRequest
     * @param \ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository $seoRepository
     */
    public function __construct(
        \Symfony\Component\HttpFoundation\Request $symfonyRequest,
        SeoRepository $seoRepository
    ) {
        parent::__construct($symfonyRequest);

        $this->seoRepository = $seoRepository;
    }

    protected function parseURI()
    {
        if ($this->getArea() === AreaEnum::AREA_FRONTEND) {
            $this->checkForUriAlias();
        }

        parent::parseURI();
    }

    /**
     * Checks, whether the current request may equals an uri alias
     */
    protected function checkForUriAlias()
    {
        list($params, $probableQuery) = $this->checkUriAliasForAdditionalParameters();

        // Nachschauen, ob ein URI-Alias für die aktuelle Seite festgelegt wurde
        $alias = $this->seoRepository->getUriByAlias(substr($probableQuery, 0, -1));
        if (!empty($alias)) {
            $this->query = $alias . $params;
        }
    }

    /**
     * Annehmen, dass ein URI Alias mit zusätzlichen Parametern übergeben wurde
     *
     * @return string[]
     */
    protected function checkUriAliasForAdditionalParameters()
    {
        $params = '';
        $probableQuery = $this->query;
        if (preg_match('/^([a-z]{1}[a-z\d\-]*\/)([a-z\d\-]+\/)*(([a-z\d\-]+)_(.+)\/)+$/', $this->query)) {
            $query = preg_split('=/=', $this->query, -1, PREG_SPLIT_NO_EMPTY);
            if (isset($query[1]) === false) {
                $query[1] = 'index';
            }
            if (isset($query[2]) === false) {
                $query[2] = 'index';
            }

            $length = 0;
            foreach ($query as $row) {
                if (strpos($row, '_') !== false) {
                    break;
                }

                $length += strlen($row) + 1;
            }
            $params = substr($this->query, $length);
            $probableQuery = substr($this->query, 0, $length);
        }

        return [$params, $probableQuery];
    }
}
