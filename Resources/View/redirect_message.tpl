{javascripts}
    {include_js module="system" file="redirect_message"}
{/javascripts}
<div id="redirect-message" class="alert alert-{if $redirect.success === true}success{else}danger{/if} text-center">
    <strong>{$redirect.text}</strong>
</div>