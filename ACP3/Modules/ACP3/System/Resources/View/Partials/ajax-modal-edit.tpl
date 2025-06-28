{if {has_permission path="`$modal.acl_resource`"}}
    {include file="asset:System/Partials/ajax-modal.tpl" modal=['id' => $modal.id, 'size' => 'xl', 'title' => $modal.title]}
    {check_access mode="link" path="`$modal.edit_path`" iconSet="solid" icon="pencil" id="`$modal.id`-link" blank=true selectors="w-100 my-3"}
{/if}
