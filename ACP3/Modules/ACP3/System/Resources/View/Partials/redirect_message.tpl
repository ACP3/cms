{foreach $redirect as $type => $messages}
    {foreach $messages as $message}
        <div id="redirect-message"
             class="alert alert-{if $type === 'success'}success{else}danger{/if} alert-dismissible fade show text-center"
             role="alert">
            <strong>{$message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{lang t="system|close"}"></button>
        </div>
    {/foreach}
{/foreach}
