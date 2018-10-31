{if !empty($redirect)}
    <div id="redirect-message"
         class="alert alert-{if $redirect.success === true}success{else}danger{/if} alert-dismissible fade show text-center"
         role="alert">
        {$redirect.text}
        <button type="button" class="close" data-dismiss="alert" aria-label="{lang t="system|close"}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
{/if}
