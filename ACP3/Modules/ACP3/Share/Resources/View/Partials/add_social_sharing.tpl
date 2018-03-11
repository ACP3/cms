{if !empty($sharing.services)}
    {load_module module="widget/share/index/index" path=$sharing.path}
    {js_libraries enable='shariff'}
{/if}
{if $sharing.ratings_active === true && !empty($sharing.rating)}
    {if !empty($sharing.services)}
        <hr>
    {/if}
    <div id="rating-wrapper">
        {include file="asset:Share/Partials/rating.tpl" rating=$sharing.rating}
    </div>
    {javascripts}
        {js_libraries enable='font-awesome'}
    {/javascripts}
{/if}
