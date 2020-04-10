<div id="comments-container">
    {include file="asset:System/Partials/form_group.button_group.tpl" options=$comments required=true label={lang t="system|allow_comments"}}
</div>
{javascripts}
    {include_js module="gallerycomments" file="partials/index.settings"}
{/javascripts}
