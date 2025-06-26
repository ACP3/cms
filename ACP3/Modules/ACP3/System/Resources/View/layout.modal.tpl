<!-- JAVASCRIPTS -->
<div class="modal-header">
    <h4 class="modal-title" id="{$modal.id}-title">{page_title}</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
</div>
<div class="modal-body">
    {event name="layout.content_before"}
    {block CONTENT}{/block}
    {event name="layout.content_after"}
</div>
