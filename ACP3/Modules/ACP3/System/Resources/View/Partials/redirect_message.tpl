{foreach $redirect as $type => $messages}
    {foreach $messages as $message}
        <div id="redirect-message"
             class="alert alert-{if $type === 'success'}success{else}danger{/if} alert-dismissable text-center"
             role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="{lang t="system|close"}">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>{$message}</strong>
        </div>
    {/foreach}
{/foreach}
