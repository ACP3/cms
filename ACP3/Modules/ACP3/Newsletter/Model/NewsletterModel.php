<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Model;


use ACP3\Core\Config;
use ACP3\Core\Date;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Model\AbstractModel;
use ACP3\Modules\ACP3\Newsletter\Installer\Schema;
use ACP3\Modules\ACP3\Newsletter\Model\Repository\NewsletterRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NewsletterModel extends AbstractModel
{
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var Secure
     */
    protected $secure;
    /**
     * @var NewsletterRepository
     */
    protected $newsletterRepository;
    /**
     * @var Config
     */
    protected $config;

    /**
     * NewsletterModel constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param Config $config
     * @param Date $date
     * @param Secure $secure
     * @param NewsletterRepository $newsletterRepository
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Config $config,
        Date $date,
        Secure $secure,
        NewsletterRepository $newsletterRepository
    ) {
        parent::__construct($eventDispatcher);

        $this->config = $config;
        $this->date = $date;
        $this->secure = $secure;
        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * @param array $formData
     * @param int $userId
     * @param int|null $newsletterId
     * @return bool|int
     */
    public function saveNewsletter(array $formData, $userId, $newsletterId = null)
    {
        $data = [
            'id' => '',
            'date' => $this->date->toSQL($formData['date']),
            'title' => $this->secure->strEncode($formData['title']),
            'text' => $this->secure->strEncode($formData['text'], true),
            'user_id' => $userId,
        ];

        if ($newsletterId === null) {
            $settings = $this->config->getSettings(Schema::MODULE_NAME);
            $data['html'] = $settings['html'];
            $data['status'] = 0;
        }

        return $this->save($this->newsletterRepository, $data, $newsletterId);
    }
}
