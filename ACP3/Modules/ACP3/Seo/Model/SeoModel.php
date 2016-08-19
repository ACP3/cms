<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Model;


use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\Seo\Model\Repository\SeoRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SeoModel extends AbstractModel
{
    /**
     * @var Secure
     */
    protected $secure;

    /**
     * SeoModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Secure $secure
     * @param SeoRepository $seoRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Secure $secure,
        SeoRepository $seoRepository
    ) {
        parent::__construct($eventDispatcher, $seoRepository);

        $this->secure = $secure;
    }

    /**
     * @param array $formData
     * @param int|null $entryId
     * @return bool|int
     */
    public function saveUriAlias(array $formData, $entryId = null)
    {
        $data = [
            'uri' => $formData['uri'],
            'alias' => $formData['alias'],
            'keywords' => $this->secure->strEncode($formData['seo_keywords']),
            'description' => $this->secure->strEncode($formData['seo_description']),
            'robots' => (int)$formData['seo_robots']
        ];

        return $this->save($data, $entryId);
    }
}
