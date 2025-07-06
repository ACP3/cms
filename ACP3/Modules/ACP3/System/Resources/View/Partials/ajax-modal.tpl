<div class="modal fade" id="{$modal.id}" tabindex="-1" role="dialog" aria-labelledby="{$modal.id}-title" aria-hidden="true" data-ajax-modal>
    <div class="modal-dialog modal-{$modal.size|default:'lg'}">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="{$modal.id}-title">{$modal.title}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                {icon iconSet="solid" icon="spinner" cssSelectors="svg-icon--spin"}
            </div>
        </div>
    </div>
</div>
{include_js module="system" file="partials/ajax-modal"}
