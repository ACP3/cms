<div class="modal fade" id="{$modal.id}" tabindex="-1" role="dialog" aria-labelledby="{$modal.id}" aria-hidden="true" data-ajax-modal>
    <div class="modal-dialog {if isset($modal.size)}{$modal.size}{else}modal-lg{/if}">
        <div class="modal-content">
            <div class="p-3 text-center">
                {icon iconSet="solid" icon="spinner" cssSelectors="svg-icon--spin"}
            </div>
        </div>
    </div>
</div>
{javascripts}
    {js_libraries enable="ajax-form"}
    {include_js module="system" file="partials/ajax-modal"}
{/javascripts}
