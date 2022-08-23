{javascripts}
    {include_js module="emoticons" file="partials/emoticons"}
{/javascripts}
<div class="my-1" data-emoticons-input="#{$emoticons_field_id}">
    {foreach $emoticons as $key => $value}
        <a href="#" title="{$key}">{$value}</a>
    {/foreach}
</div>
