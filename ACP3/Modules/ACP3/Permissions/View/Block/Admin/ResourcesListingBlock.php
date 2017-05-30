<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\View\Block\Admin;


use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Modules;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Permissions\Model\Repository\AclResourcesRepository;

class ResourcesListingBlock extends AbstractBlock
{
    /**
     * @var ACLInterface
     */
    private $acl;
    /**
     * @var Modules
     */
    private $modules;
    /**
     * @var AclResourcesRepository
     */
    private $resourceRepository;

    /**
     * ResourcesListingBlock constructor.
     * @param BlockContext $context
     * @param ACLInterface $acl
     * @param Modules $modules
     * @param AclResourcesRepository $resourceRepository
     */
    public function __construct(
        BlockContext $context,
        ACLInterface $acl,
        Modules $modules,
        AclResourcesRepository $resourceRepository
    ) {
        parent::__construct($context);

        $this->acl = $acl;
        $this->modules = $modules;
        $this->resourceRepository = $resourceRepository;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $resources = $this->resourceRepository->getAllResources();
        $cResources = count($resources);
        $output = [];
        for ($i = 0; $i < $cResources; ++$i) {
            if ($this->modules->isActive($resources[$i]['module_name']) === true) {
                $module = $this->translator->t($resources[$i]['module_name'], $resources[$i]['module_name']);
                $output[$module][] = $resources[$i];
            }
        }
        ksort($output);

        return [
            'resources' => $output,
            'can_delete_resource' => $this->acl->hasPermission('admin/permissions/resources/delete'),
            'can_edit_resource' => $this->acl->hasPermission('admin/permissions/resources/edit')
        ];
    }
}
