{if {has_permission path="`$modal.acl_resource`"}}
    {include file="asset:System/Partials/ajax-modal.tpl" modal=['id' => $modal.id, 'size' => 'xl', 'title' => $modal.title]}
    {check_access mode="link" path="`$modal.edit_path`" iconSet="solid" icon=$modal.button_icon|default:'pencil' id="`$modal.id`-link" blank=true selectors=$modal.button_selectors|default:"w-100 my-3" class=$modal.button_icon_selectors|default:''}
{/if}
