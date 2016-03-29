<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core;

use ACP3\Core\Breadcrumb;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\RouterInterface;
use ACP3\Core\User;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

/**
 * Class Pagination
 * @package ACP3\Modules\ACP3\Seo\Core
 */
class Pagination extends \ACP3\Core\Pagination
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;

    /**
     * Pagination constructor.
     *
     * @param \ACP3\Core\User                                $user
     * @param \ACP3\Core\Breadcrumb                          $breadcrumb
     * @param \ACP3\Core\I18n\Translator                     $translator
     * @param \ACP3\Core\Http\RequestInterface               $request
     * @param \ACP3\Core\RouterInterface                     $router
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements   $metaStatements
     */
    public function __construct(
        User $user,
        Breadcrumb $breadcrumb,
        Translator $translator,
        RequestInterface $request,
        RouterInterface $router,
        MetaStatements $metaStatements
    ) {

        parent::__construct($user, $breadcrumb, $translator, $request, $router);

        $this->metaStatements = $metaStatements;
    }

    /**
     * @internal param string $link
     */
    protected function setMetaStatements()
    {
        parent::setMetaStatements();

        // Vorherige und nächste Seite für Suchmaschinen und Prefetching propagieren
        if ($this->request->getArea() !== AreaEnum::AREA_ADMIN) {
            $path = ($this->request->getArea() === AreaEnum::AREA_ADMIN ? 'acp/' : '') . $this->request->getUriWithoutPages();
            $link = $this->router->route($path);
            if ($this->currentPage - 1 > 0) {
                // Seitenangabe in der Seitenbeschreibung ab Seite 2 angeben
                $this->metaStatements->setDescriptionPostfix(
                    $this->translator->t(
                        'system',
                        'page_x',
                        ['%page%' => $this->currentPage]
                    )
                );
                $this->metaStatements->setPreviousPage($link . 'page_' . ($this->currentPage - 1) . '/');
            }
            if ($this->currentPage + 1 <= $this->totalPages) {
                $this->metaStatements->setNextPage($link . 'page_' . ($this->currentPage + 1) . '/');
            }
            if ($this->request->getParameters()->get('page', 0) === 1) {
                $this->metaStatements->setCanonicalUri($link);
            }
        }
    }
}
