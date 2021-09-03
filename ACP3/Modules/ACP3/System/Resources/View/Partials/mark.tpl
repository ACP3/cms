<div class="modal fade js-mass-delete-modal" id="{$dataGridIdentifier}-mass-delete-modal" tabindex="-1" aria-labelledby="{$dataGridIdentifier}-mass-delete-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{$dataGridIdentifier}-mass-delete-modal-label">{lang t="system|delete_marked"}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{lang t="system|close"}"></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{lang t="system|no"}</button>
                <button type="submit" class="btn btn-danger" data-bs-dismiss="modal" id="{$dataGridIdentifier}-mass-delete-modal-confirm-button">{lang t="system|yes"}</button>
            </div>
        </div>
    </div>
</div>

{javascripts}
    {include_js module="system" file="partials/mass-action"}
{/javascripts}
