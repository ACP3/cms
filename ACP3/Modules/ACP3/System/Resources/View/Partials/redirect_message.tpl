{if !empty($redirect)}
    <div id="redirect-message"
         class="alert alert-{if $redirect.success === true}success{else}danger{/if} alert-dismissable text-center"
         role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="{lang t="system|close"}">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>{$redirect.text}</strong>
    </div>
{/if}
