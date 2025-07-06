{if !empty($sharing)}
    {if $sharing.ratings_active === true}
        <hr>
        <div id="rating-wrapper">
            {include file="asset:Share/Partials/rating.tpl" rating=$sharing.rating}
        </div>
    {/if}
    {if $sharing.active === true}
        <hr>
        <button class="btn btn-secondary share-button rounded-0" data-share-url="{uri args=$sharing.path absolute=true}">
            {icon iconSet="solid" icon="share"}
            {lang t="share|share_url"}
        </button>
        {include_js module="share" file="widget/index.index"}
    {/if}
{/if}
