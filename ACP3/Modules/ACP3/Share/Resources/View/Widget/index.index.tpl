{javascripts}
    {include_js module="share" file="widget/index.index"}
{/javascripts}
<button class="btn btn-secondary share-button rounded-0" data-share-url="{uri args=$sharing.path absolute=true}">
    {icon iconSet="solid" icon="share"}
    {lang t="share|share_url"}
</button>
