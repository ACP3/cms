{if !empty($sharing) && $sharing.ratings_active === true}
    <hr>
    <div id="rating-wrapper">
        {include file="asset:Share/Partials/rating.tpl" rating=$sharing.rating}
    </div>
    {javascripts}
    {include_js module="share" file="partials/rating"}
    {/javascripts}
{/if}
{if !empty($sharing.services)}
    <hr>
    {load_module module="widget/share/index/index" path=$sharing.path}
    {js_libraries enable='shariff'}
{/if}
